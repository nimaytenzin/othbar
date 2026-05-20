<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Pages\FinancialReport;
use App\Filament\Pages\ReceivePayment;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Filament\Widgets\InvoiceStatsOverview;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public function getSubheading(): string | Htmlable | null
    {
        return 'Track receivables, record payments, and audit invoice history.';
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'outstanding';
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            InvoiceStatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [
            'outstanding' => Tab::make('Outstanding')
                ->icon(Heroicon::OutlinedBanknotes)
                ->badge(fn (): int => Invoice::query()->outstanding()->count())
                ->badgeColor(fn (): string => Invoice::query()->outstanding()->exists() ? 'warning' : 'gray')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->outstanding()),
            'overdue' => Tab::make('Overdue')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->badge(fn (): int => Invoice::query()->overdue()->count())
                ->badgeColor(fn (): string => Invoice::query()->overdue()->exists() ? 'danger' : 'gray')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->overdue()),
            'partial' => Tab::make('Partial')
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->badge(fn (): int => Invoice::query()->partiallyPaid()->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->partiallyPaid()),
            'paid' => Tab::make('Paid')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->badge(fn (): int => Invoice::query()->paid()->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->paid()),
            'void' => Tab::make('Void')
                ->icon(Heroicon::OutlinedXCircle)
                ->badge(fn (): int => Invoice::query()->void()->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->void()),
            'all' => Tab::make('All')
                ->icon(Heroicon::OutlinedQueueList)
                ->badge(fn (): int => Invoice::query()->count()),
        ];
    }

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table, $this);
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
                    ->extraAttributes(['class' => 'oth-invoices-list-panel']),
            ]);
    }

    public function getTabsContentComponent(): Component
    {
        return parent::getTabsContentComponent()
            ->extraAttributes(['class' => 'oth-invoices-list-tabs']);
    }

    public function getPageClasses(): array
    {
        return [
            ...parent::getPageClasses(),
            'oth-invoices-list-page',
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()?->can('payments.receive')) {
            $actions[] = Action::make('receivePayment')
                ->label('Receive payment')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('primary')
                ->url(ReceivePayment::getUrl());
        }

        if (Auth::user()?->can('reports.view')) {
            $actions[] = Action::make('financialReport')
                ->label('Financial report')
                ->icon(Heroicon::OutlinedChartBar)
                ->color('gray')
                ->outlined()
                ->url(FinancialReport::getUrl());
        }

        if (Auth::user()?->can('invoices.manage')) {
            $actions[] = CreateAction::make()
                ->label('Issue invoice')
                ->color('gray')
                ->outlined();
        }

        return $actions;
    }
}
