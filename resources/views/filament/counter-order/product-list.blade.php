@php
    /** @var list<array{line_type: string, line_index: int, description: string, subtitle: ?string, quantity: int, unit_price_minor: int, discount_minor: int, tax_rate_percent: float, tax_minor: int, line_total_minor: int, stock_quantity: ?int}> $orderLineRows */
    /** @var array<int, array{id: int, name: string, sku: ?string, unit_price_minor: int, stock_quantity: int, image_url: ?string}> $productSearchResults */
    /** @var array<int|string, string> $productOptions */
    /** @var array{id: int, name: string, sku: ?string, unit_price_minor: int, stock_quantity: int, image_url: ?string}|null $selectedProduct */
    $lineCount = count($orderLineRows);
    $itemCount = collect($orderLineRows)->sum('quantity');
@endphp

<div class="oth-counter-products">
    <section class="oth-card oth-counter-products__panel oth-counter-order-lines">
        <div class="oth-counter-order-lines__toolbar">
            <div>
                <p class="oth-counter-products__step">Step 1</p>
                <h3 class="oth-card__title" style="margin:0;">Order lines</h3>
                <p class="oth-card__subtitle" style="margin:0.35rem 0 0;">
                    {{ $lineCount }} line{{ $lineCount === 1 ? '' : 's' }} · {{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}
                </p>
            </div>
            <div class="oth-counter-order-lines__actions">
                <button
                    type="button"
                    class="fi-btn fi-btn-size-sm fi-color-gray"
                    wire:click="mountAction('addCustomLine')"
                >
                    + Custom line
                </button>
            </div>
        </div>

        @if($orderLineRows === [])
            <div class="oth-counter-products__empty">
                <p>No lines added yet.</p>
                <p class="oth-counter-products__empty-hint">Add products below or click <strong>+ Custom line</strong> for packaging and other charges.</p>
            </div>
        @else
            <div class="oth-table-wrap oth-counter-order-lines__table">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit price</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">GST</th>
                            <th class="text-right">Line total</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderLineRows as $row)
                            <tr wire:key="order-line-{{ $row['line_type'] }}-{{ $row['line_index'] }}">
                                <td>
                                    <span class="oth-counter-order-lines__type oth-counter-order-lines__type--{{ $row['line_type'] }}">
                                        {{ $row['line_type'] === 'custom' ? 'Custom' : 'Product' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $row['description'] }}</strong>
                                    @if(filled($row['subtitle']))
                                        <div class="oth-counter-products__sku">{{ $row['subtitle'] }}</div>
                                    @endif
                                    @if($row['line_type'] === 'product' && $row['stock_quantity'] !== null)
                                        <div class="oth-counter-products__sku">Stock: {{ $row['stock_quantity'] }}</div>
                                    @endif
                                </td>
                                <td class="text-right">{{ $row['quantity'] }}</td>
                                <td class="text-right">Nu. {{ number_format($row['unit_price_minor'] / 100, 2) }}</td>
                                <td class="text-right">
                                    @if($row['discount_minor'] > 0)
                                        − Nu. {{ number_format($row['discount_minor'] / 100, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($row['tax_rate_percent'], 1) }}%
                                    <div class="oth-counter-products__sku">Nu. {{ number_format($row['tax_minor'] / 100, 2) }}</div>
                                </td>
                                <td class="text-right"><strong>Nu. {{ number_format($row['line_total_minor'] / 100, 2) }}</strong></td>
                                <td class="text-right">
                                    <div class="oth-counter-order-lines__row-actions">
                                        @if($row['line_type'] === 'product')
                                            <button
                                                type="button"
                                                class="fi-btn fi-btn-size-xs fi-color-gray"
                                                wire:click="mountAction('editProductLine', { index: {{ $row['line_index'] }} })"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="fi-btn fi-btn-size-xs fi-color-danger"
                                                wire:click="removeLine({{ $row['line_index'] }})"
                                            >
                                                Remove
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                class="fi-btn fi-btn-size-xs fi-color-gray"
                                                wire:click="mountAction('editCustomLine', { index: {{ $row['line_index'] }} })"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="fi-btn fi-btn-size-xs fi-color-danger"
                                                wire:click="removeCustomLine({{ $row['line_index'] }})"
                                            >
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="oth-card oth-counter-products__panel">
        <div class="oth-counter-products__header">
            <div>
                <h3 class="oth-card__title" style="margin:0;">Add products</h3>
                <p class="oth-card__subtitle" style="margin:0.35rem 0 0;">Search by name or SKU, or pick from the full product list.</p>
            </div>
        </div>

        <div class="oth-counter-products__chooser">
            <label class="oth-counter-products__field">
                <span class="oth-counter-products__label">Search products</span>
                <input
                    type="search"
                    class="oth-counter-products__search"
                    wire:model.live.debounce.300ms="productSearch"
                    placeholder="Type name or SKU..."
                    autocomplete="off"
                    @disabled($selectedProduct)
                />
            </label>

            <label class="oth-counter-products__field">
                <span class="oth-counter-products__label">Browse all products</span>
                <select wire:model.live="selectedProductId" class="oth-counter-products__select" @disabled($selectedProduct)>
                    <option value="">Choose a product...</option>
                    @foreach($productOptions as $productId => $label)
                        <option value="{{ $productId }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="oth-counter-products__picker">
            @if($selectedProduct)
                <div class="oth-counter-products__selected">
                    <div class="oth-counter-products__product">
                        @if($selectedProduct['image_url'])
                            <img src="{{ $selectedProduct['image_url'] }}" alt="" class="oth-counter-products__thumb">
                        @else
                            <div class="oth-counter-products__thumb oth-counter-products__thumb--empty"></div>
                        @endif
                        <div>
                            <strong>{{ $selectedProduct['name'] }}</strong>
                            <div class="oth-counter-products__selected-meta">
                                @if($selectedProduct['sku'])
                                    <span>{{ $selectedProduct['sku'] }}</span>
                                    <span aria-hidden="true">·</span>
                                @endif
                                <span>Nu. {{ number_format($selectedProduct['unit_price_minor'] / 100, 2) }}</span>
                                <span aria-hidden="true">·</span>
                                <span>Stock: {{ $selectedProduct['stock_quantity'] }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="clearSelectedProduct" class="oth-btn oth-btn--secondary oth-btn--sm">
                        Change
                    </button>
                </div>
            @elseif(trim($productSearch) !== '')
                <div class="oth-counter-products__results" wire:loading.class="is-loading">
                    <div wire:loading wire:target="productSearch" class="oth-counter-products__results-status">
                        Searching...
                    </div>
                    <div wire:loading.remove wire:target="productSearch">
                        @if($productSearchResults === [])
                            <p class="oth-counter-products__results-empty">No products match “{{ $productSearch }}”.</p>
                        @else
                            <ul class="oth-counter-products__results-list">
                                @foreach($productSearchResults as $product)
                                    <li wire:key="product-search-{{ $product['id'] }}">
                                        <button
                                            type="button"
                                            class="oth-counter-products__result"
                                            wire:click="selectProduct({{ $product['id'] }})"
                                        >
                                            <span class="oth-counter-products__product">
                                                @if($product['image_url'])
                                                    <img src="{{ $product['image_url'] }}" alt="" class="oth-counter-products__thumb">
                                                @else
                                                    <div class="oth-counter-products__thumb oth-counter-products__thumb--empty"></div>
                                                @endif
                                                <span>
                                                    <strong>{{ $product['name'] }}</strong>
                                                    <span class="oth-counter-products__result-meta">
                                                        @if($product['sku'])
                                                            {{ $product['sku'] }} ·
                                                        @endif
                                                        Nu. {{ number_format($product['unit_price_minor'] / 100, 2) }} · Stock {{ $product['stock_quantity'] }}
                                                    </span>
                                                </span>
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="oth-counter-products__add-form">
            <label class="oth-counter-products__field">
                <span class="oth-counter-products__label">Quantity</span>
                <input
                    type="number"
                    min="1"
                    class="oth-counter-products__qty oth-counter-products__qty--add"
                    wire:model="addQuantity"
                    @disabled(! $selectedProduct)
                />
            </label>

            <div class="oth-counter-products__field oth-counter-products__field--action">
                <span class="oth-counter-products__label" aria-hidden="true">&nbsp;</span>
                <button
                    type="button"
                    wire:click="addSelectedProduct"
                    wire:loading.attr="disabled"
                    class="oth-btn oth-btn--primary"
                    @disabled(! $selectedProduct)
                >
                    <span wire:loading.remove wire:target="addSelectedProduct">Add to order</span>
                    <span wire:loading wire:target="addSelectedProduct">Adding...</span>
                </button>
            </div>
        </div>
    </section>
</div>

<style>
    .oth-counter-order-lines__toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }
    .oth-counter-order-lines__actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .oth-counter-order-lines__table {
        margin-top: 0.25rem;
    }
    .oth-counter-order-lines__row-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .oth-counter-order-lines__type {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.15rem 0.45rem;
        border-radius: 0.25rem;
        white-space: nowrap;
    }
    .oth-counter-order-lines__type--product {
        background: rgba(30, 58, 42, 0.1);
        color: #1e3a2a;
    }
    .oth-counter-order-lines__type--custom {
        background: rgba(196, 132, 60, 0.15);
        color: #92400e;
    }
    .dark .oth-counter-order-lines__type--product {
        background: rgba(255, 255, 255, 0.08);
        color: #a7f3d0;
    }
    .dark .oth-counter-order-lines__type--custom {
        background: rgba(251, 191, 36, 0.15);
        color: #fcd34d;
    }
    .oth-table .text-right { text-align: right; }
    .oth-table-wrap { overflow-x: auto; }
    .oth-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .oth-table th,
    .oth-table td {
        padding: 0.65rem 0.75rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        vertical-align: top;
    }
    .dark .oth-table th,
    .dark .oth-table td {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }
    .oth-table th {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6b7280;
        background: rgba(0, 0, 0, 0.02);
    }
    .dark .oth-table th {
        background: rgba(255, 255, 255, 0.04);
    }
</style>
