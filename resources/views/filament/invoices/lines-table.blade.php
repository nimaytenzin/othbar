@php
    /** @var list<array{description: string, quantity: int, unit_price_minor: int, discount_minor: int, tax_rate_percent: float, tax_minor: int, line_total_minor: int, product_name: ?string}> $rows */
    /** @var array{subtotal_minor: int, discount_minor: int, tax_minor: int, total_minor: int} $totals */
    $lineCount = count($rows);
@endphp

<div class="oth-invoice-lines">
    <div class="oth-invoice-lines__toolbar">
        <div>
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white" style="margin:0;">Line items</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" style="margin:0.25rem 0 0;">
                {{ $lineCount }} line{{ $lineCount === 1 ? '' : 's' }} on this invoice
            </p>
        </div>
        <button
            type="button"
            class="fi-btn fi-btn-size-sm fi-color-primary"
            wire:click="mountAction('addLineItem')"
        >
            + Add line item
        </button>
    </div>

    @if($lineCount === 0)
        <div class="oth-invoice-lines__empty fi-ta-empty-state">
            <p class="text-sm text-gray-500 dark:text-gray-400">No line items yet. Click <strong>Add line item</strong> to start.</p>
        </div>
    @else
        <div class="oth-table-wrap">
            <table class="oth-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">GST</th>
                        <th class="text-right">Line total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $index => $row)
                        <tr wire:key="invoice-line-{{ $index }}">
                            <td>
                                <strong>{{ $row['description'] }}</strong>
                                @if($row['product_name'])
                                    <div class="text-xs text-gray-500">{{ $row['product_name'] }}</div>
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
                                <div class="text-xs text-gray-500">Nu. {{ number_format($row['tax_minor'] / 100, 2) }}</div>
                            </td>
                            <td class="text-right"><strong>Nu. {{ number_format($row['line_total_minor'] / 100, 2) }}</strong></td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        type="button"
                                        class="fi-btn fi-btn-size-xs fi-color-gray"
                                        wire:click="mountAction('editLineItem', { index: {{ $index }} })"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="fi-btn fi-btn-size-xs fi-color-danger"
                                        wire:click="removeLine({{ $index }})"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="oth-counter-summary oth-invoice-lines__totals">
            <dl class="oth-counter-summary__grid">
                <div>
                    <dt>Subtotal</dt>
                    <dd>Nu. {{ number_format($totals['subtotal_minor'] / 100, 2) }}</dd>
                </div>
                @if($totals['discount_minor'] > 0)
                    <div>
                        <dt>Discount</dt>
                        <dd class="oth-counter-summary__discount">− Nu. {{ number_format($totals['discount_minor'] / 100, 2) }}</dd>
                    </div>
                @endif
                @if($totals['tax_minor'] > 0)
                    <div>
                        <dt>GST</dt>
                        <dd>Nu. {{ number_format($totals['tax_minor'] / 100, 2) }}</dd>
                    </div>
                @endif
                <div class="oth-counter-summary__total">
                    <dt>Invoice total</dt>
                    <dd>Nu. {{ number_format($totals['total_minor'] / 100, 2) }}</dd>
                </div>
            </dl>
        </div>
    @endif
</div>

<style>
    .oth-invoice-lines { display: flex; flex-direction: column; gap: 1rem; }
    .oth-invoice-lines__toolbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .oth-invoice-lines__empty { padding: 2rem 1rem; text-align: center; border: 1px dashed rgba(0,0,0,.12); border-radius: .5rem; }
    .oth-invoice-lines__totals { margin-top: .5rem; }
    .oth-table-wrap { overflow-x: auto; }
    .oth-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .oth-table th, .oth-table td { padding: .5rem .75rem; border-bottom: 1px solid rgba(0,0,0,.08); vertical-align: top; }
    .oth-table th { text-align: left; font-size: .75rem; text-transform: uppercase; letter-spacing: .03em; color: #6b7280; }
    .oth-table .text-right { text-align: right; }
</style>
