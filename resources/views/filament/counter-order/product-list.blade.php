@php
    /** @var array<int, array{product_id: int, quantity: int, name: string, sku: ?string, unit_price_minor: int, line_total_minor: int, stock_quantity: int, image_url: ?string}> $cartLines */
    /** @var array<int, array{id: int, name: string, sku: ?string, unit_price_minor: int, stock_quantity: int, image_url: ?string}> $productSearchResults */
    /** @var array<int|string, string> $productOptions */
    /** @var array{id: int, name: string, sku: ?string, unit_price_minor: int, stock_quantity: int, image_url: ?string}|null $selectedProduct */
    $lineCount = count($cartLines);
    $itemCount = collect($cartLines)->sum('quantity');
@endphp

<div class="oth-counter-products">
    <section class="oth-card oth-counter-products__panel">
        <div class="oth-counter-products__header">
            <div>
                <p class="oth-counter-products__step">Step 1</p>
                <h3 class="oth-card__title" style="margin:0;">Add products</h3>
                <p class="oth-card__subtitle" style="margin:0.35rem 0 0;">Search by name or SKU, or pick from the full product list.</p>
            </div>
            @if($lineCount > 0)
                <span class="oth-counter-products__badge">{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</span>
            @endif
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

    <section class="oth-card oth-counter-products__panel oth-counter-products__panel--flush">
        <div class="oth-card__header">
            <h3 class="oth-card__title">Order lines</h3>
            <p class="oth-card__subtitle">{{ $lineCount }} product line{{ $lineCount === 1 ? '' : 's' }} in this sale</p>
        </div>

        @if($cartLines === [])
            <div class="oth-counter-products__empty">
                <p>No products added yet.</p>
                <p class="oth-counter-products__empty-hint">Search or choose a product above to build the order.</p>
            </div>
        @else
            <div class="oth-table-wrap">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Stock</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Line total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartLines as $index => $line)
                            <tr wire:key="cart-line-{{ $line['product_id'] }}-{{ $index }}">
                                <td>
                                    <div class="oth-counter-products__product">
                                        @if($line['image_url'])
                                            <img src="{{ $line['image_url'] }}" alt="" class="oth-counter-products__thumb">
                                        @else
                                            <div class="oth-counter-products__thumb oth-counter-products__thumb--empty"></div>
                                        @endif
                                        <div>
                                            <strong>{{ $line['name'] }}</strong>
                                            @if($line['sku'])
                                                <div class="oth-counter-products__sku">{{ $line['sku'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">Nu. {{ number_format($line['unit_price_minor'] / 100, 2) }}</td>
                                <td class="text-right">{{ $line['stock_quantity'] }}</td>
                                <td class="text-right">
                                    <input
                                        type="number"
                                        min="1"
                                        class="oth-counter-products__qty"
                                        value="{{ $line['quantity'] }}"
                                        wire:change="updateLineQuantity({{ $index }}, $event.target.value)"
                                    />
                                </td>
                                <td class="text-right"><strong>Nu. {{ number_format($line['line_total_minor'] / 100, 2) }}</strong></td>
                                <td class="text-right">
                                    <button type="button" wire:click="removeLine({{ $index }})" class="oth-btn oth-btn--danger oth-btn--sm">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
