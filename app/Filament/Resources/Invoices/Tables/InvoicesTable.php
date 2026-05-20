<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Filament\Pages\ReceivePayment;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoicesTable
{
    public static function configure(Table $table, ?ListInvoices $livewire = null): Table
    {
        return $table
            ->columns(static::columns($livewire))
            ->defaultSort(
                fn (): string => in_array($livewire?->activeTab, ['outstanding', 'overdue'], true)
                    ? 'due_date'
                    : 'issue_date',
                fn (): string => in_array($livewire?->activeTab, ['outstanding', 'overdue'], true)
                    ? 'asc'
                    : 'desc',
            )
            ->filters(static::filters(), layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns([
                'default' => 1,
                'sm' => 2,
                'lg' => 4,
            ])
            ->filtersTriggerAction(fn (Action $action): Action => $action
                ->button()
                ->outlined()
                ->label('Filters')
                ->icon(Heroicon::OutlinedFunnel))
            ->deferFilters(false)
            ->striped()
            ->recordClasses(fn (Invoice $record): ?string => $record->isOverdue() ? 'fi-ta-row--overdue' : null)
            ->emptyStateHeading(fn (): string => static::emptyStateHeading($livewire))
            ->emptyStateDescription(fn (): string => static::emptyStateDescription($livewire))
            ->emptyStateIcon(fn (): string => static::emptyStateIcon($livewire))
            ->recordActions([
                ViewAction::make(),
                Action::make('collect')
                    ->label('Collect')
                    ->button()
                    ->size('sm')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->url(fn (Invoice $record): string => ReceivePayment::getUrl().'?'.http_build_query([
                        'customer_id' => $record->customer_id,
                        'invoice_id' => $record->id,
                    ]))
                    ->visible(fn (Invoice $record): bool => $record->balanceDueMinor() > 0
                        && $record->status !== InvoiceStatus::Void
                        && (Auth::user()?->can('payments.receive') ?? false)),
            ]);
    }

    /**
     * @return array<int, TextColumn>
     */
    protected static function columns(?ListInvoices $livewire): array
    {
        $tab = $livewire?->activeTab ?? 'outstanding';
        $collectionFocus = in_array($tab, ['outstanding', 'overdue'], true);
        $showOverdueHint = in_array($tab, ['outstanding', 'overdue', 'all'], true);

        return [
            TextColumn::make('number')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::SemiBold)
                ->copyable()
                ->copyMessage('Invoice number copied'),
            TextColumn::make('customer.display_name')
                ->label('Customer')
                ->searchable()
                ->sortable()
                ->limit(32)
                ->tooltip(fn (Invoice $record): ?string => strlen($record->customer?->display_name ?? '') > 32
                    ? $record->customer->display_name
                    : null),
            TextColumn::make('source')
                ->label('Source')
                ->badge()
                ->state(fn (Invoice $record): string => $record->order_id ? 'order' : 'manual')
                ->formatStateUsing(fn (string $state): string => $state === 'order' ? 'Order' : 'Manual')
                ->color(fn (string $state): string => $state === 'order' ? 'info' : 'gray')
                ->toggleable(isToggledHiddenByDefault: $collectionFocus),
            TextColumn::make('issue_date')
                ->label('Issued')
                ->date('M j, Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: $collectionFocus),
            TextColumn::make('due_date')
                ->label('Due')
                ->date('M j, Y')
                ->sortable()
                ->placeholder('—')
                ->color(fn (Invoice $record): ?string => $record->isOverdue() ? 'danger' : null)
                ->weight(fn (Invoice $record): ?FontWeight => $record->isOverdue() ? FontWeight::SemiBold : null),
            TextColumn::make('days_overdue')
                ->label('Days late')
                ->state(fn (Invoice $record): ?int => $record->daysOverdue())
                ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : $state.'d')
                ->badge()
                ->color('danger')
                ->visible($showOverdueHint)
                ->toggleable(isToggledHiddenByDefault: $tab !== 'overdue'),
            TextColumn::make('status')
                ->badge()
                ->formatStateUsing(fn (InvoiceStatus $state): string => $state->getLabel())
                ->color(fn (InvoiceStatus $state, Invoice $record): string => $record->isOverdue() && $state !== InvoiceStatus::Paid && $state !== InvoiceStatus::Void
                    ? 'danger'
                    : $state->getColor())
                ->icon(fn (InvoiceStatus $state): ?string => $state->getIcon())
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: in_array($tab, ['paid', 'void'], true)),
            TextColumn::make('total_minor')
                ->label('Total')
                ->formatStateUsing(fn ($state) => Invoice::formatMinor((int) $state))
                ->sortable()
                ->alignment('end')
                ->toggleable(isToggledHiddenByDefault: $collectionFocus),
            TextColumn::make('amount_paid_minor')
                ->label('Paid')
                ->formatStateUsing(fn ($state) => Invoice::formatMinor((int) $state))
                ->toggleable(isToggledHiddenByDefault: $tab !== 'partial')
                ->sortable()
                ->alignment('end'),
            TextColumn::make('balance_due')
                ->label('Balance due')
                ->state(fn (Invoice $record): int => $record->balanceDueMinor())
                ->formatStateUsing(fn ($state) => Invoice::formatMinor((int) $state))
                ->color(fn ($state): string => (int) $state > 0 ? 'danger' : 'success')
                ->weight(fn ($state): FontWeight => (int) $state > 0 ? FontWeight::Bold : FontWeight::Medium)
                ->alignment('end')
                ->visible(! in_array($tab, ['paid', 'void'], true))
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->orderByRaw(
                        '(total_minor - amount_paid_minor) '.($direction === 'desc' ? 'desc' : 'asc')
                    );
                }),
            TextColumn::make('order.number')
                ->label('Order')
                ->url(fn (Invoice $record): ?string => $record->order_id && (Auth::user()?->can('orders.view') ?? false)
                    ? OrderResource::getUrl('view', ['record' => $record->order_id])
                    : null)
                ->color('primary')
                ->toggleable(),
        ];
    }

    /**
     * @return array<int, Filter|SelectFilter>
     */
    protected static function filters(): array
    {
        return [
            SelectFilter::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'display_name')
                ->searchable()
                ->preload()
                ->columnSpan(['lg' => 2]),
            SelectFilter::make('source')
                ->label('Source')
                ->options([
                    'order' => 'From order',
                    'manual' => 'Manual',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return match ($data['value'] ?? null) {
                        'order' => $query->fromOrder(),
                        'manual' => $query->manual(),
                        default => $query,
                    };
                }),
            Filter::make('issue_date_range')
                ->label('Issue date')
                ->columnSpan(['lg' => 2])
                ->schema([
                    DatePicker::make('from')
                        ->label('Issued from')
                        ->native(false)
                        ->placeholder('Start'),
                    DatePicker::make('to')
                        ->label('Issued to')
                        ->maxDate(today())
                        ->native(false)
                        ->placeholder('End'),
                ])
                ->columns(2)
                ->query(function (Builder $query, array $data): Builder {
                    return $query->issuedBetween($data['from'] ?? null, $data['to'] ?? null);
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (filled($data['from'] ?? null)) {
                        $indicators[] = Indicator::make('From: '.Carbon::parse($data['from'])->format('M j, Y'))
                            ->removeField('from');
                    }

                    if (filled($data['to'] ?? null)) {
                        $indicators[] = Indicator::make('To: '.Carbon::parse($data['to'])->format('M j, Y'))
                            ->removeField('to');
                    }

                    return $indicators;
                }),
            Filter::make('due_date_range')
                ->label('Due date')
                ->columnSpan(['lg' => 2])
                ->schema([
                    DatePicker::make('from')
                        ->label('Due from')
                        ->native(false)
                        ->placeholder('Start'),
                    DatePicker::make('to')
                        ->label('Due to')
                        ->native(false)
                        ->placeholder('End'),
                ])
                ->columns(2)
                ->query(function (Builder $query, array $data): Builder {
                    if (filled($data['from'] ?? null)) {
                        $query->whereDate('due_date', '>=', $data['from']);
                    }

                    if (filled($data['to'] ?? null)) {
                        $query->whereDate('due_date', '<=', $data['to']);
                    }

                    return $query;
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (filled($data['from'] ?? null)) {
                        $indicators[] = Indicator::make('Due from: '.Carbon::parse($data['from'])->format('M j, Y'))
                            ->removeField('from');
                    }

                    if (filled($data['to'] ?? null)) {
                        $indicators[] = Indicator::make('Due to: '.Carbon::parse($data['to'])->format('M j, Y'))
                            ->removeField('to');
                    }

                    return $indicators;
                }),
        ];
    }

    protected static function emptyStateHeading(?ListInvoices $livewire): string
    {
        return match ($livewire?->activeTab) {
            'overdue' => 'No overdue invoices',
            'partial' => 'No partially paid invoices',
            'paid' => 'No paid invoices yet',
            'void' => 'No voided invoices',
            'all' => 'No invoices',
            default => 'All caught up',
        };
    }

    protected static function emptyStateDescription(?ListInvoices $livewire): string
    {
        return match ($livewire?->activeTab) {
            'overdue' => 'Every open invoice is still within its payment terms.',
            'partial' => 'No invoices have a partial payment on record.',
            'paid' => 'Paid invoices will appear here once customers settle in full.',
            'void' => 'Voided invoices are kept for audit when you cancel an invoice.',
            'all' => 'Issue an invoice from an order or create one manually to get started.',
            default => 'There are no open balances right now.',
        };
    }

    protected static function emptyStateIcon(?ListInvoices $livewire): string
    {
        return match ($livewire?->activeTab) {
            'overdue' => 'heroicon-o-check-circle',
            'paid' => 'heroicon-o-banknotes',
            'void' => 'heroicon-o-archive-box',
            default => 'heroicon-o-document-text',
        };
    }
}
