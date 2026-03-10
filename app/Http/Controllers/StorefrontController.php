<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shopper\Cart\CartManager;
use Shopper\Cart\Exceptions\InvalidDiscountException;
use Shopper\Cart\Exceptions\InsufficientStockException;
use Shopper\Cart\Facades\Cart;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\PaymentStatus;
use Shopper\Core\Models\Category;
use Shopper\Core\Models\Collection;
use Shopper\Core\Models\Order;
use Shopper\Core\Models\OrderAddress;
use Shopper\Core\Models\OrderItem;
use Shopper\Core\Models\Product;

class StorefrontController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::with(['prices', 'media'])
            ->where('is_visible', true)
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->withCount('products')
            ->take(6)
            ->get();

        $collections = Collection::take(3)->get();

        return view('storefront.home', compact('featuredProducts', 'categories', 'collections'));
    }

    public function shop(Request $request)
    {
        $query = Product::with(['prices', 'media', 'categories'])
            ->where('is_visible', true);

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::whereNull('parent_id')->get();

        return view('storefront.shop', compact('products', 'categories'));
    }

    public function product(string $slug)
    {
        $product = Product::with(['prices', 'media', 'categories'])
            ->where('slug', $slug)
            ->where('is_visible', true)
            ->firstOrFail();

        $related = Product::with(['prices', 'media'])
            ->where('is_visible', true)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }

    public function collection(string $slug)
    {
        $collection = Collection::where('slug', $slug)->firstOrFail();
        $products = Product::with(['prices', 'media'])
            ->where('is_visible', true)
            ->latest()
            ->paginate(12);

        return view('storefront.collection', compact('collection', 'products'));
    }

    public function cart()
    {
        $cart = Cart::current();
        $cartLines = $cart ? $cart->lines()->with('purchasable')->get() : collect([]);

        return view('storefront.cart', compact('cart', 'cartLines'));
    }

    public function addToCart(Request $request, CartManager $cartManager)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (! $product->allow_backorder && ! $product->inStock((int) $request->quantity)) {
            $stock = $product->stock;
            $msg = $stock > 0
                ? "Only {$stock} left in stock. Please reduce the quantity."
                : 'This product is currently out of stock.';
            return back()->with('error', $msg)->withInput();
        }

        $cart = Cart::current() ?? Cart::create();

        try {
            $cartManager->add($cart, $product, (int) $request->quantity);
        } catch (InsufficientStockException $e) {
            $stock = $product->stock;
            $msg = $stock > 0
                ? "Only {$stock} in stock. Reduce quantity to add to basket."
                : 'This product is currently out of stock.';
            return back()->with('error', $msg)->withInput();
        }

        return redirect()->route('cart')->with('success', $product->name . ' added to basket!');
    }

    public function updateCartLine(Request $request, int $line, CartManager $cartManager)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::current();
        if (! $cart) {
            return redirect()->route('cart')->with('error', 'Cart not found.');
        }

        try {
            $cartManager->update($cart, $line, ['quantity' => (int) $request->quantity]);
        } catch (\Exception $e) {
            return redirect()->route('cart')->with('error', $e->getMessage());
        }

        return redirect()->route('cart');
    }

    public function removeCartLine(int $line, CartManager $cartManager)
    {
        $cart = Cart::current();
        if ($cart) {
            $cartManager->remove($cart, $line);
        }

        return redirect()->route('cart')->with('success', 'Item removed from basket.');
    }

    public function applyCoupon(Request $request, CartManager $cartManager)
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        $cart = Cart::current();
        if (! $cart) {
            return redirect()->route('cart')->with('coupon_error', 'Cart not found.');
        }

        try {
            $cartManager->applyCoupon($cart, strtoupper(trim($request->coupon_code)));
        } catch (InvalidDiscountException $e) {
            return redirect()->route('cart')->with('coupon_error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('cart')->with('coupon_error', 'Invalid or expired coupon code.');
        }

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function removeCoupon(CartManager $cartManager)
    {
        $cart = Cart::current();
        if ($cart) {
            $cartManager->removeCoupon($cart);
        }

        return redirect()->route('cart')->with('success', 'Coupon removed.');
    }

    public function checkout()
    {
        $cart = Cart::current();
        $cartLines = $cart ? $cart->lines()->with('purchasable')->get() : collect([]);

        if ($cartLines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your basket is empty.');
        }

        return view('storefront.checkout', compact('cart', 'cartLines'));
    }

    public function placeOrder(Request $request, CartManager $cartManager)
    {
        $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'phone'            => 'required|string|max:30',
            'email'            => 'nullable|email|max:150',
            'street_address'   => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'postal_code'      => 'required|string|max:20',
            'notes'            => 'nullable|string|max:1000',
            'payment_proof'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_reference'=> 'nullable|string|max:100',
        ], [
            'payment_proof.mimes' => 'Payment proof must be a JPG, PNG, or PDF file.',
            'payment_proof.max'   => 'Payment proof file must be under 5 MB.',
        ]);

        // Require either proof screenshot or reference number
        if (! $request->hasFile('payment_proof') && ! $request->filled('payment_reference')) {
            return back()->withErrors([
                'payment_proof' => 'Please upload your payment screenshot or enter the transaction reference number.',
            ])->withInput();
        }

        $cart = Cart::current();
        if (! $cart) {
            return redirect()->route('cart')->with('error', 'Your basket is empty.');
        }

        $cartLines = $cart->lines()->with('purchasable')->get();
        if ($cartLines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your basket is empty.');
        }

        // Handle payment proof upload
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $subtotal = $cartLines->sum(fn ($l) => (int) round($l->unit_price_amount * 100) * $l->quantity);

        $order = Order::create([
            'number'            => 'OTH-' . strtoupper(substr(uniqid(), -6)),
            'price_amount'      => $subtotal,
            'currency_code'     => $cart->currency_code ?? 'BTN',
            'status'            => OrderStatus::New,
            'payment_status'    => PaymentStatus::Pending,
            'notes'             => $request->notes,
            'payment_proof_path'=> $proofPath,
            'payment_reference' => $request->payment_reference,
            'metadata'          => json_encode([
                'email'          => $request->email,
                'payment_method' => 'bank-transfer',
                'coupon_code'    => $cart->coupon_code,
            ]),
        ]);

        $address = OrderAddress::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'street_address' => $request->street_address,
            'city'           => $request->city,
            'postal_code'    => $request->postal_code,
            'phone'          => $request->phone,
            'country_name'   => 'Bhutan',
        ]);

        $order->update(['shipping_address_id' => $address->id]);

        foreach ($cartLines as $line) {
            OrderItem::create([
                'order_id'          => $order->id,
                'name'              => $line->purchasable->name ?? 'Product',
                'quantity'          => $line->quantity,
                'unit_price_amount' => $line->unit_price_amount,
                'sku'               => $line->purchasable->sku ?? '',
                'product_id'        => $line->purchasable_id,
                'product_type'      => $line->purchasable_type,
            ]);
        }

        $cartManager->clear($cart);
        $cart->update(['completed_at' => now()]);
        Cart::forget();

        return redirect()->route('checkout.confirmation', $order->id);
    }

    public function orderConfirmation(int $order)
    {
        $order = Order::with('items')->findOrFail($order);

        return view('storefront.confirmation', compact('order'));
    }

    public function story()
    {
        return view('storefront.story');
    }
}
