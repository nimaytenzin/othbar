<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InvalidCouponException;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\CartSessionService;
use App\Support\PaymentChannels;
use App\Support\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorefrontController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::query()
            ->with(['media', 'brand', 'categories'])
            ->where('is_visible', true)
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::query()->whereNull('parent_id')
            ->withCount('products')
            ->take(6)
            ->get();

        return view('storefront.home', compact('featuredProducts', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::query()->with(['media', 'categories', 'brand'])
            ->where('is_visible', true);

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $sort = $request->string('sort')->toString();
        match ($sort) {
            'price_asc' => $query->orderBy('price_minor')->orderBy('name'),
            'price_desc' => $query->orderByDesc('price_minor')->orderBy('name'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::query()->whereNull('parent_id')->get();
        $activeCategory = $request->filled('category')
            ? Category::query()->where('slug', $request->category)->first()
            : null;

        return view('storefront.shop', compact('products', 'categories', 'activeCategory'));
    }

    public function product(string $slug)
    {
        $product = Product::query()->with(['media', 'categories', 'brand'])
            ->where('slug', $slug)
            ->where('is_visible', true)
            ->firstOrFail();

        $related = Product::query()->with(['media', 'brand'])
            ->where('is_visible', true)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }

    public function collection(string $slug)
    {
        $collection = Collection::query()->where('slug', $slug)->firstOrFail();
        $products = Product::query()->with(['media', 'brand'])
            ->where('is_visible', true)
            ->latest()
            ->paginate(12);

        return view('storefront.collection', compact('collection', 'products'));
    }

    public function cart(CartSessionService $cartService)
    {
        $cart = $cartService->cartViewModel();
        $cartLines = $cartService->linesWithProducts();
        $pricing = $cartService->pricingTotals();
        extract($this->cartPricingViewData($pricing));

        return view('storefront.cart', compact('cart', 'cartLines', 'subtotalMinor', 'discountMinor', 'gstMinor', 'effectiveTaxRate', 'totalMinor'));
    }

    public function addToCart(Request $request, CartSessionService $cartService)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::query()->findOrFail($request->product_id);

        if (! $product->allow_backorder && ! $product->inStock((int) $request->quantity)) {
            $stock = $product->stock;
            $msg = $stock > 0
                ? "Only {$stock} left in stock. Please reduce the quantity."
                : 'This product is currently out of stock.';

            return back()->with('error', $msg)->withInput();
        }

        $cartService->addProduct($product, (int) $request->quantity);

        return redirect()->route('cart')->with('success', $product->name.' added to basket!');
    }

    public function updateCartLine(Request $request, int $line, CartSessionService $cartService)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $lines = $cartService->lineRows();
        if (! isset($lines[$line])) {
            return redirect()->route('cart')->with('error', 'Cart line not found.');
        }
        $row = $lines[$line];
        $product = Product::query()->find($row['product_id']);
        if (! $product) {
            return redirect()->route('cart')->with('error', 'Product no longer available.');
        }
        if (! $product->allow_backorder && (int) $request->quantity > $product->stock) {
            $stock = $product->stock;
            $msg = $stock > 0
                ? "Only {$stock} in stock. Reduce quantity to update your basket."
                : 'This product is currently out of stock.';

            return redirect()->route('cart')->with('error', $msg);
        }

        $cartService->updateLineQuantity($line, (int) $request->quantity);

        return redirect()->route('cart');
    }

    public function removeCartLine(int $line, CartSessionService $cartService)
    {
        $cartService->removeLine($line);

        return redirect()->route('cart')->with('success', 'Item removed from basket.');
    }

    public function applyCoupon(Request $request, CartSessionService $cartService)
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        if ($cartService->lineRows() === []) {
            return redirect()->route('cart')->with('coupon_error', 'Cart is empty.');
        }

        try {
            $cartService->applyCouponCode(strtoupper(trim($request->coupon_code)));
        } catch (InvalidCouponException $e) {
            return redirect()->route('cart')->with('coupon_error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('cart')->with('coupon_error', 'Invalid or expired coupon code.');
        }

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function removeCoupon(CartSessionService $cartService)
    {
        $cartService->removeCoupon();

        return redirect()->route('cart')->with('success', 'Coupon removed.');
    }

    public function checkout(CartSessionService $cartService)
    {
        $cartLines = $cartService->linesWithProducts();
        if ($cartLines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your basket is empty.');
        }

        $cart = $cartService->cartViewModel();
        $pricing = $cartService->pricingTotals();
        extract($this->cartPricingViewData($pricing));

        return view('storefront.checkout', compact('cart', 'cartLines', 'subtotalMinor', 'discountMinor', 'gstMinor', 'effectiveTaxRate', 'totalMinor'));
    }

    public function placeOrder(Request $request, CartSessionService $cartService)
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'notes' => 'nullable|string|max:1000',
            'fulfillment_method' => 'required|in:delivery,pickup',
        ];

        if ($request->input('fulfillment_method') === 'delivery') {
            $rules['street_address'] = 'required|string|max:255';
            $rules['city'] = 'required|string|max:100';
            $rules['postal_code'] = 'required|string|max:20';
        }

        $request->validate($rules);

        $cartLines = $cartService->linesWithProducts();
        if ($cartLines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your basket is empty.');
        }

        $fulfillmentMethod = $request->input('fulfillment_method');

        $streetAddress = $fulfillmentMethod === 'pickup'
            ? (SiteSetting::current()->pickup_address_label ?: config('payments.pickup_address_label'))
            : $request->street_address;
        $city = $fulfillmentMethod === 'pickup'
            ? 'Thimphu'
            : $request->city;
        $postalCode = $fulfillmentMethod === 'pickup'
            ? 'N/A'
            : $request->postal_code;

        $payToken = Str::random(48);
        $pricing = $cartService->pricingTotals();
        $coupon = $cartService->resolvedCoupon();

        $order = Order::query()->create([
            'number' => 'OTH-'.strtoupper(substr(uniqid(), -6)),
            'total_minor' => $pricing['total_minor'],
            'currency_code' => 'BTN',
            'status' => OrderStatus::New,
            'payment_status' => PaymentStatus::Pending,
            'notes' => $request->notes,
            'payment_proof_path' => null,
            'payment_reference' => null,
            'payment_access_token' => $payToken,
            'fulfillment_method' => $fulfillmentMethod,
            'metadata' => [
                'email' => $request->email,
                'payment_method' => PaymentMethods::MODE_BANK_TRANSFER,
                'coupon_code' => $cartService->couponCode(),
                'source' => 'storefront',
                'fulfillment_method' => $fulfillmentMethod,
                'subtotal_minor' => $pricing['subtotal_minor'],
                'discount_minor' => $pricing['discount_minor'],
                'gst_minor' => $pricing['gst_minor'],
                'effective_tax_rate' => $pricing['effective_tax_rate'],
                'tax_breakdown' => $pricing['tax_breakdown'],
            ],
        ]);

        $address = OrderAddress::query()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'street_address' => $streetAddress,
            'city' => $city,
            'postal_code' => $postalCode,
            'phone' => $request->phone,
            'country_name' => 'Bhutan',
        ]);

        $order->update(['shipping_address_id' => $address->id]);

        foreach ($cartLines as $item) {
            $p = $item->purchasable;
            if (! $p) {
                return redirect()->route('cart')->with('error', 'A product in your basket is no longer available.');
            }
            OrderItem::query()->create([
                'order_id' => $order->id,
                'name' => $p->name,
                'quantity' => $item->quantity,
                'unit_price_minor' => $item->unit_price_amount,
                'sku' => $p->sku ?? '',
                'product_id' => $p->id,
            ]);
        }

        if ($coupon && $cartService->couponApplies($coupon)) {
            $coupon->increment('uses_count');
        }

        $cartService->clear();

        return redirect()->route('checkout.pay', [
            'order' => $order->id,
            'token' => $payToken,
        ]);
    }

    public function showPay(int $order, string $token)
    {
        $order = Order::query()->with(['items', 'shippingAddress'])->findOrFail($order);
        $this->assertPayToken($order, $token);

        if ($order->payment_proof_path) {
            return redirect()->route('checkout.confirmation', [
                'order' => $order->id,
                'token' => $token,
            ]);
        }

        $paymentApps = PaymentChannels::paymentApps();
        $merchantAccount = PaymentChannels::merchantAccount();
        $totalNu = $order->total_minor / 100;

        return view('storefront.pay', [
            'order' => $order,
            'token' => $token,
            'paymentApps' => $paymentApps,
            'merchantAccount' => $merchantAccount,
            'totalNu' => $totalNu,
        ]);
    }

    public function submitPaymentProof(Request $request, int $order, string $token)
    {
        $order = Order::query()->findOrFail($order);
        $this->assertPayToken($order, $token);

        $request->validate([
            'payment_bank' => ['required', Rule::in(PaymentMethods::bankIds())],
            'payment_reference' => 'required|string|max:255',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'payment_bank.required' => 'Select the mobile banking app you used for this transfer.',
            'payment_bank.in' => 'Select a valid mobile banking app ('.PaymentMethods::paymentAppNames().').',
            'payment_reference.required' => 'Enter your transaction or journal number.',
            'payment_proof.required' => 'Please upload a screenshot of your payment confirmation.',
            'payment_proof.mimes' => 'Payment proof must be a JPG, PNG, or PDF file.',
            'payment_proof.max' => 'Payment proof file must be under 5 MB.',
        ]);

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof_path' => $proofPath,
            'payment_reference' => $request->payment_reference,
            'metadata' => array_merge($order->metadata ?? [], PaymentMethods::metadataPayload(
                PaymentMethods::MODE_BANK_TRANSFER,
                $request->payment_bank,
            )),
        ]);

        return redirect()->route('checkout.confirmation', [
            'order' => $order->id,
            'token' => $token,
        ]);
    }

    public function orderConfirmation(int $order, string $token)
    {
        $order = Order::query()->with('items')->findOrFail($order);
        $this->assertPayToken($order, $token);

        return view('storefront.confirmation', compact('order', 'token'));
    }

    public function staffLogin()
    {
        return view('storefront.staff-login', [
            'adminLoginUrl' => url('/admin/login'),
        ]);
    }

    public function story()
    {
        return view('storefront.story');
    }

    private function assertPayToken(Order $order, string $token): void
    {
        if (! $order->payment_access_token || ! hash_equals((string) $order->payment_access_token, $token)) {
            abort(404);
        }
    }

    /**
     * @param  array{subtotal_minor: int, discount_minor: int, gst_minor: int, total_minor: int, effective_tax_rate: float}  $pricing
     * @return array{subtotalMinor: int, discountMinor: int, gstMinor: int, effectiveTaxRate: float, totalMinor: int}
     */
    private function cartPricingViewData(array $pricing): array
    {
        return [
            'subtotalMinor' => $pricing['subtotal_minor'],
            'discountMinor' => $pricing['discount_minor'],
            'gstMinor' => $pricing['gst_minor'],
            'effectiveTaxRate' => $pricing['effective_tax_rate'],
            'totalMinor' => $pricing['total_minor'],
        ];
    }
}
