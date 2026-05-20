<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\FinancialReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class FinancialReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Financial report';

    protected static ?string $title = 'Financial report';

    protected static ?string $slug = 'financial-report';

    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 5;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('reports.view');
    }

    public function mount(): void
    {
        $this->form->fill($this->defaultFormState());
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultFormState(): array
    {
        return [
            'period_preset' => 'month',
            ...$this->defaultDateRange(),
        ];
    }

    /**
     * @return array{date_from: string, date_to: string}
     */
    protected function defaultDateRange(): array
    {
        return [
            'date_from' => today()->startOfMonth()->toDateString(),
            'date_to' => today()->toDateString(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Financial report';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('period_preset')
                    ->label('Period')
                    ->options([
                        'month' => 'This month',
                        'fiscal_year' => 'This fiscal year',
                        'custom' => 'Custom range',
                    ])
                    ->default('month')
                    ->live()
                    ->afterStateUpdated(function (?string $state, callable $set): void {
                        if ($state === 'fiscal_year') {
                            $range = app(FinancialReportService::class)->fiscalYearDateRange();
                            $set('date_from', $range['date_from']);
                            $set('date_to', $range['date_to']);
                        } elseif ($state === 'month') {
                            $set('date_from', today()->startOfMonth()->toDateString());
                            $set('date_to', today()->toDateString());
                        }
                    }),
                DatePicker::make('date_from')
                    ->label('From date')
                    ->required()
                    ->native(false)
                    ->visible(fn (): bool => ($this->data['period_preset'] ?? 'month') === 'custom')
                    ->maxDate(fn (): ?string => $this->data['date_to'] ?? today()->toDateString()),
                DatePicker::make('date_to')
                    ->label('To date')
                    ->required()
                    ->native(false)
                    ->visible(fn (): bool => ($this->data['period_preset'] ?? 'month') === 'custom')
                    ->minDate(fn (): ?string => $this->data['date_from'] ?? null)
                    ->maxDate(today()),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
            SchemaView::make('filament.pages.financial-report-results')
                ->viewData(fn (): array => $this->reportViewData()),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('financial-report-form')
            ->livewireSubmitHandler('generateReport')
            ->footer([
                Actions::make([
                    Action::make('generateReport')
                        ->label('Generate report')
                        ->submit('generateReport')
                        ->icon(Heroicon::OutlinedMagnifyingGlass),
                ])
                    ->alignment('start')
                    ->key('financial-report-actions'),
            ]);
    }

    public function generateReport(): void
    {
        $state = $this->resolvedDateRange();
        $this->form->fill([
            ...$this->data,
            ...$state,
        ]);

        Notification::make()
            ->title('Report updated')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function reportViewData(): array
    {
        $range = $this->resolvedDateRange();
        $service = app(FinancialReportService::class);

        return [
            'dateFrom' => $range['date_from'],
            'dateTo' => $range['date_to'],
            'arSummary' => $service->accountsReceivableSummary(),
            'totalOutstandingMinor' => $service->totalOutstandingMinor(),
            'overdueInvoices' => $service->overdueInvoices(),
            'gstSummary' => $service->gstSummary($range),
            'paymentsReceived' => $service->paymentsReceived($range),
            'isGstRegistered' => (bool) SiteSetting::current()->is_gst_registered,
        ];
    }

    /**
     * @return array{date_from: string, date_to: string}
     */
    protected function resolvedDateRange(): array
    {
        $preset = $this->data['period_preset'] ?? 'month';

        if ($preset === 'fiscal_year') {
            return app(FinancialReportService::class)->fiscalYearDateRange();
        }

        if ($preset === 'month') {
            return [
                'date_from' => today()->startOfMonth()->toDateString(),
                'date_to' => today()->toDateString(),
            ];
        }

        $defaults = $this->defaultDateRange();
        $from = $this->data['date_from'] ?? $defaults['date_from'];
        $to = $this->data['date_to'] ?? $defaults['date_to'];
        $today = today()->toDateString();

        if ($to > $today) {
            $to = $today;
        }

        if ($from > $to) {
            $from = $to;
        }

        return [
            'date_from' => Carbon::parse($from)->toDateString(),
            'date_to' => Carbon::parse($to)->toDateString(),
        ];
    }
}
