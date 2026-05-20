<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Pages\CreateCounterOrder;
use App\Filament\Pages\OrderReport;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    #[Url(as: 'day')]
    public ?string $orderDay = null;

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->loadDefaultActiveTab();

        if (filled($this->orderDay)) {
            $this->tableFilters = array_merge($this->tableFilters ?? [], [
                'order_day' => ['date' => $this->orderDay],
            ]);
        } else {
            $this->syncOrderDayToFilters();
        }
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();

        $this->cachedTabs = [];

        if ($this->tabUsesDayFilter()) {
            $this->orderDay ??= today()->toDateString();
            $this->syncOrderDayToFilters();
        }
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon(Heroicon::OutlinedQueueList)
                ->badge(fn (): int => Order::query()->count()),
            'pending' => Tab::make('Pending')
                ->icon(Heroicon::OutlinedClock)
                ->badge(fn (): int => Order::query()->pending()->count())
                ->badgeColor(fn (): string => Order::query()->pending()->exists() ? 'warning' : 'gray')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->pending()),
            'completed' => Tab::make('Completed')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->badge(fn (): int => Order::query()->completedOnDay($this->resolveOrderDay())->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->completedOnDay($this->resolveOrderDay())),
            'cancelled' => Tab::make('Cancelled')
                ->icon(Heroicon::OutlinedXCircle)
                ->badge(fn (): int => Order::query()->cancelledOnDay($this->resolveOrderDay())->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->cancelledOnDay($this->resolveOrderDay())),
        ];
    }

    public function updatedTableFilters(): void
    {
        $date = data_get($this->tableFilters, 'order_day.date');

        if (filled($date)) {
            $this->orderDay = $date;
        }

        $this->cachedTabs = [];

        parent::updatedTableFilters();
    }

    protected function syncOrderDayToFilters(): void
    {
        if (! $this->tabUsesDayFilter()) {
            return;
        }

        $this->orderDay ??= today()->toDateString();

        $this->tableFilters = array_merge($this->tableFilters ?? [], [
            'order_day' => [
                'date' => $this->orderDay,
            ],
        ]);
    }

    public function tabUsesDayFilter(): bool
    {
        return in_array($this->activeTab, ['completed', 'cancelled'], true);
    }

    public function resolveOrderDay(): string
    {
        if (filled($this->orderDay)) {
            return $this->orderDay;
        }

        $date = data_get($this->tableFilters, 'order_day.date');

        if (filled($date)) {
            return $date;
        }

        return today()->toDateString();
    }

    public function table(Table $table): Table
    {
        return OrdersTable::configure($table, $this);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        $this->getTabsContentComponent(),
                        RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                        EmbeddedTable::make(),
                        RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
                    ])
                    ->compact()
                    ->extraAttributes(['class' => 'oth-orders-list-panel']),
            ]);
    }

    public function getTabsContentComponent(): Component
    {
        return parent::getTabsContentComponent()
            ->extraAttributes(['class' => 'oth-orders-list-tabs']);
    }

    public function getPageClasses(): array
    {
        return [
            ...parent::getPageClasses(),
            'oth-orders-list-page',
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()?->can('orders.create')) {
            $actions[] = Action::make('createCounterOrder')
                ->label('Create order')
                ->icon('heroicon-o-plus')
                ->url(CreateCounterOrder::getUrl());
        }

        if (Auth::user()?->can('reports.view')) {
            $actions[] = Action::make('orderReport')
                ->label('Order report')
                ->icon('heroicon-o-document-chart-bar')
                ->url(OrderReport::getUrl());
        }

        return $actions;
    }
}
