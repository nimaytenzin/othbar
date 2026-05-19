<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Product;
use App\Services\CounterOrderService;
use App\Support\PaymentMethods;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class CreateCounterOrder extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Counter order';

    protected static ?string $title = 'Counter order';

    protected static ?string $slug = 'counter-order';

    protected static string|UnitEnum|null $navigationGroup = 'Store';

    protected static ?int $navigationSort = 0;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?int $selectedProductId = null;

    public int $addQuantity = 1;

    public string $productSearch = '';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('orders.create');
    }

    public function mount(): void
    {
        $this->form->fill([
            'items' => [],
            'payment_method' => PaymentMethods::MODE_CASH,
            'payment_bank' => null,
            'manual_discount' => null,
            'coupon_code' => null,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Counter order';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Build an in-store sale in four steps: products, payment, customer, receipt.';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Wizard::make([
                    Step::make('Products')
                        ->label('Products')
                        ->description('Add items and apply discounts')
                        ->icon(Heroicon::OutlinedShoppingBag)
                        ->afterValidation(function (): void {
                            if ($this->getCartLines() === []) {
                                throw ValidationException::withMessages([
                                    'items' => 'Add at least one product to the order.',
                                ]);
                            }
                        })
                        ->schema([
                            Hidden::make('items')
                                ->default([]),
                            SchemaView::make('product_list')
                                ->view('filament.counter-order.product-list')
                                ->viewData(fn (): array => [
                                    'cartLines' => $this->getCartLines(),
                                    'productSearch' => $this->productSearch,
                                    'productSearchResults' => $this->getProductSearchResults(),
                                    'productOptions' => $this->getProductOptions(),
                                    'selectedProduct' => $this->getSelectedProductPreview(),
                                ]),
                            Section::make('Discounts')
                                ->description('Optional coupon or manual discount for this sale')
                                ->icon(Heroicon::OutlinedReceiptPercent)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('coupon_code')
                                        ->label('Coupon code')
                                        ->placeholder('e.g. WELCOME10')
                                        ->maxLength(50)
                                        ->live(onBlur: true),
                                    TextInput::make('manual_discount')
                                        ->label('Manual discount (Nu.)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->prefix('Nu.')
                                        ->live(onBlur: true),
                                ]),
                        ]),
                    Step::make('Payment')
                        ->label('Payment')
                        ->description('Cash or bank transfer via '.PaymentMethods::paymentAppNames())
                        ->icon(Heroicon::OutlinedBanknotes)
                        ->afterValidation(function (): void {
                            PaymentMethods::validateCounterPayment(
                                (string) ($this->data['payment_method'] ?? PaymentMethods::MODE_CASH),
                                filled($this->data['payment_bank'] ?? null) ? (string) $this->data['payment_bank'] : null,
                                filled($this->data['payment_reference'] ?? null) ? (string) $this->data['payment_reference'] : null,
                            );
                        })
                        ->schema([
                            Hidden::make('payment_method')
                                ->default(PaymentMethods::MODE_CASH),
                            Hidden::make('payment_bank'),
                            SchemaView::make('payment_methods')
                                ->view('filament.counter-order.payment-methods')
                                ->viewData(fn (): array => [
                                    'selected' => $this->data['payment_method'] ?? PaymentMethods::MODE_CASH,
                                    'selectedBank' => $this->data['payment_bank'] ?? null,
                                ]),
                            TextInput::make('payment_reference')
                                ->label('Transaction ref / journal number')
                                ->placeholder('e.g. JRN-2026-00123')
                                ->required(fn (Get $get): bool => ($get('payment_method') ?? PaymentMethods::MODE_CASH) === PaymentMethods::MODE_BANK_TRANSFER)
                                ->maxLength(255)
                                ->visible(fn (Get $get): bool => ($get('payment_method') ?? PaymentMethods::MODE_CASH) === PaymentMethods::MODE_BANK_TRANSFER)
                                ->columnSpanFull(),
                        ]),
                    Step::make('Customer')
                        ->label('Customer')
                        ->description('Who is this sale for?')
                        ->icon(Heroicon::OutlinedUser)
                        ->columns(2)
                        ->schema([
                            TextInput::make('first_name')
                                ->label('First name')
                                ->required()
                                ->maxLength(100),
                            TextInput::make('last_name')
                                ->label('Last name')
                                ->required()
                                ->maxLength(100),
                            TextInput::make('phone')
                                ->label('Phone')
                                ->tel()
                                ->required()
                                ->maxLength(30),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(150),
                            Textarea::make('notes')
                                ->label('Order notes')
                                ->placeholder('Optional notes for this sale')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    Step::make('Receipt')
                        ->label('Receipt')
                        ->description('Review and complete the sale')
                        ->icon(Heroicon::OutlinedPrinter)
                        ->schema([
                            SchemaView::make('receipt_review')
                                ->view('filament.counter-order.review')
                                ->viewData(fn (): array => [
                                    'pricing' => $this->resolvePricingSummary(),
                                    'cartLines' => $this->getCartLines(),
                                    'customer' => [
                                        'first_name' => $this->data['first_name'] ?? null,
                                        'last_name' => $this->data['last_name'] ?? null,
                                        'phone' => $this->data['phone'] ?? null,
                                        'email' => $this->data['email'] ?? null,
                                        'notes' => $this->data['notes'] ?? null,
                                    ],
                                    'payment' => [
                                        'payment_method' => $this->data['payment_method'] ?? null,
                                        'payment_bank' => $this->data['payment_bank'] ?? null,
                                        'payment_reference' => $this->data['payment_reference'] ?? null,
                                    ],
                                ]),
                        ]),
                ])
                    ->submitAction(
                        Action::make('completeCounterOrder')
                            ->label('Complete sale & print receipt')
                            ->action('completeCounterOrder')
                            ->extraAttributes(['class' => 'oth-counter-submit'])
                    )
                    ->nextAction(fn (Action $action): Action => $action->label('Continue'))
                    ->previousAction(fn (Action $action): Action => $action->label('Back'))
                    ->alpineSubmitHandler('$wire.completeCounterOrder()')
                    ->contained(true)
                    ->extraAttributes(['class' => 'oth-counter-wizard']),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Grid::make(['default' => 1, 'xl' => 3])
            ->schema([
                EmbeddedSchema::make('form')
                    ->columnSpan(['xl' => 2]),
                SchemaView::make('counter_sidebar')
                    ->view('filament.counter-order.sidebar')
                    ->viewData(fn (): array => $this->getSidebarData())
                    ->columnSpan(['xl' => 1]),
            ])
            ->extraAttributes(['class' => 'oth-counter-layout']);
    }

    public function addSelectedProduct(): void
    {
        if ($this->selectedProductId === null) {
            Notification::make()
                ->title('Select a product')
                ->body('Choose a product from the dropdown before adding.')
                ->warning()
                ->send();

            return;
        }

        $productId = (int) $this->selectedProductId;
        $quantity = max(1, $this->addQuantity);
        $product = Product::query()->findOrFail($productId);

        $items = $this->data['items'] ?? [];
        $existingIndex = null;

        foreach ($items as $index => $item) {
            if ((int) ($item['product_id'] ?? 0) === $productId) {
                $existingIndex = $index;
                break;
            }
        }

        $targetQuantity = $existingIndex !== null
            ? (int) ($items[$existingIndex]['quantity'] ?? 1) + $quantity
            : $quantity;

        if (! $product->inStock($targetQuantity)) {
            Notification::make()
                ->title('Insufficient stock')
                ->body("Only {$product->stock_quantity} unit(s) available for {$product->name}.")
                ->danger()
                ->send();

            return;
        }

        if ($existingIndex !== null) {
            $items[$existingIndex]['quantity'] = $targetQuantity;
        } else {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $this->data['items'] = array_values($items);
        $this->selectedProductId = null;
        $this->productSearch = '';
        $this->addQuantity = 1;
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::query()->find($productId);

        if ($product === null) {
            return;
        }

        $this->selectedProductId = $product->id;
        $this->productSearch = '';
    }

    public function updatedSelectedProductId(mixed $value): void
    {
        if (filled($value)) {
            $this->productSearch = '';

            return;
        }

        $this->selectedProductId = null;
    }

    public function clearSelectedProduct(): void
    {
        $this->selectedProductId = null;
        $this->productSearch = '';
    }

    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     sku: ?string,
     *     unit_price_minor: int,
     *     stock_quantity: int,
     *     image_url: ?string
     * }>
     */
    public function getProductSearchResults(): array
    {
        $term = trim($this->productSearch);

        if (mb_strlen($term) < 1) {
            return [];
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return Product::query()
            ->where(function ($query) use ($like): void {
                $query->where('name', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            })
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit_price_minor' => $product->price_minor,
                'stock_quantity' => $product->stock_quantity,
                'image_url' => $product->featuredImageUrl(),
            ])
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     sku: ?string,
     *     unit_price_minor: int,
     *     stock_quantity: int,
     *     image_url: ?string
     * }|null
     */
    public function getSelectedProductPreview(): ?array
    {
        if ($this->selectedProductId === null) {
            return null;
        }

        $product = Product::query()->find($this->selectedProductId);

        if ($product === null) {
            return null;
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit_price_minor' => $product->price_minor,
            'stock_quantity' => $product->stock_quantity,
            'image_url' => $product->featuredImageUrl(),
        ];
    }

    public function updateLineQuantity(int $index, mixed $quantity): void
    {
        $items = $this->data['items'] ?? [];

        if (! isset($items[$index])) {
            return;
        }

        $quantity = max(1, (int) $quantity);
        $product = Product::query()->find($items[$index]['product_id'] ?? null);

        if ($product !== null && ! $product->inStock($quantity)) {
            Notification::make()
                ->title('Insufficient stock')
                ->body("Only {$product->stock_quantity} unit(s) available for {$product->name}.")
                ->danger()
                ->send();

            return;
        }

        $items[$index]['quantity'] = $quantity;
        $this->data['items'] = array_values($items);
    }

    public function removeLine(int $index): void
    {
        $items = $this->data['items'] ?? [];

        if (! isset($items[$index])) {
            return;
        }

        unset($items[$index]);
        $this->data['items'] = array_values($items);
    }

    /**
     * @return list<array{
     *     product_id: int,
     *     quantity: int,
     *     name: string,
     *     sku: ?string,
     *     unit_price_minor: int,
     *     line_total_minor: int,
     *     stock_quantity: int,
     *     image_url: ?string
     * }>
     */
    public function getCartLines(): array
    {
        $items = $this->data['items'] ?? [];

        if ($items === []) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', collect($items)->pluck('product_id')->filter()->all())
            ->get()
            ->keyBy('id');

        return collect($items)
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null))
            ->map(function (array $item) use ($products): ?array {
                $product = $products->get((int) $item['product_id']);

                if ($product === null) {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                return [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price_minor' => $product->price_minor,
                    'line_total_minor' => $product->price_minor * $quantity,
                    'stock_quantity' => $product->stock_quantity,
                    'image_url' => $product->featuredImageUrl(),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string, string>
     */
    public function getProductOptions(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Product $product): array => [
                $product->id => "{$product->name} — Nu. ".number_format($product->price_minor / 100, 2)." (stock: {$product->stock_quantity})",
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getSidebarData(): array
    {
        $cartLines = $this->getCartLines();

        return [
            'pricing' => $this->resolvePricingSummary(),
            'cartLines' => $cartLines,
            'itemCount' => (int) collect($cartLines)->sum('quantity'),
            'customer' => [
                'first_name' => $this->data['first_name'] ?? null,
                'last_name' => $this->data['last_name'] ?? null,
                'phone' => $this->data['phone'] ?? null,
            ],
            'payment' => [
                'payment_method' => $this->data['payment_method'] ?? null,
                'payment_bank' => $this->data['payment_bank'] ?? null,
                'payment_reference' => $this->data['payment_reference'] ?? null,
            ],
        ];
    }

    public function completeCounterOrder(CounterOrderService $counterOrderService): void
    {
        try {
            $formData = $this->form->getState();

            $order = $counterOrderService->createAndComplete(
                $formData,
                Auth::user(),
                $formData['payment_method'],
                $formData['payment_reference'] ?? null,
                $formData['payment_bank'] ?? null,
            );

            Notification::make()
                ->title('Counter sale completed')
                ->body("Order {$order->number} is paid and fulfilled.")
                ->success()
                ->send();

            $this->redirect(route('filament.admin.orders.receipt', $order).'?autoprint=1');
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('Could not complete sale')
                ->body(collect($exception->errors())->flatten()->first() ?? 'Please check the form and try again.')
                ->danger()
                ->send();

            throw $exception;
        }
    }

    public function savePending(CounterOrderService $counterOrderService): void
    {
        $formData = $this->form->getState();

        unset($formData['payment_method'], $formData['payment_bank'], $formData['payment_reference']);

        $order = $counterOrderService->createPending($formData, Auth::user());

        Notification::make()
            ->title('Counter order saved')
            ->body("Order {$order->number} is pending payment.")
            ->success()
            ->send();

        $this->redirect(OrderResource::getUrl('view', ['record' => $order]));
    }

    /**
     * @return array<string, mixed>
     */
    public function resolvePricingSummary(): array
    {
        $items = collect($this->data['items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null))
            ->map(fn (array $item): array => [
                'product_id' => (int) $item['product_id'],
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            ])
            ->values()
            ->all();

        if ($items === []) {
            return [
                'subtotal_minor' => 0,
                'discount_minor' => 0,
                'manual_discount_minor' => 0,
                'coupon_code' => null,
                'gst_minor' => 0,
                'gst_percentage' => 0,
                'total_minor' => 0,
            ];
        }

        try {
            return app(CounterOrderService::class)->calculateTotals(
                $items,
                $this->data['coupon_code'] ?? null,
                (int) round(((float) ($this->data['manual_discount'] ?? 0)) * 100),
            );
        } catch (ValidationException) {
            return [
                'subtotal_minor' => 0,
                'discount_minor' => 0,
                'manual_discount_minor' => 0,
                'coupon_code' => null,
                'gst_minor' => 0,
                'gst_percentage' => 0,
                'total_minor' => 0,
                'coupon_error' => true,
            ];
        }
    }
}
