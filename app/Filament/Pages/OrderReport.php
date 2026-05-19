<?php

namespace App\Filament\Pages;

use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class OrderReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Order report';

    protected static ?string $title = 'Order report';

    protected static ?string $slug = 'order-report';

    protected static string|UnitEnum|null $navigationGroup = 'Store';

    protected static ?int $navigationSort = 3;

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
        $this->form->fill($this->defaultDateRange());
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

    /**
     * @return array{date_from: string, date_to: string}
     */
    protected function reportDateRange(): array
    {
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

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Order report';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('date_from')
                    ->label('From date')
                    ->required()
                    ->native(false)
                    ->maxDate(fn (): ?string => $this->data['date_to'] ?? today()->toDateString()),
                DatePicker::make('date_to')
                    ->label('To date')
                    ->required()
                    ->native(false)
                    ->minDate(fn (): ?string => $this->data['date_from'] ?? null)
                    ->maxDate(today()),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
            SchemaView::make('filament.pages.order-report-results')
                ->viewData(fn (): array => $this->reportViewData()),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('order-report-form')
            ->livewireSubmitHandler('generateReport')
            ->footer([
                Actions::make([
                    Action::make('generateReport')
                        ->label('Generate report')
                        ->submit('generateReport')
                        ->icon(Heroicon::OutlinedMagnifyingGlass),
                    Action::make('exportCsv')
                        ->label('Export CSV')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action('exportCsv')
                        ->color('gray'),
                ])
                    ->alignment('start')
                    ->key('order-report-actions'),
            ]);
    }

    public function generateReport(): void
    {
        $state = $this->validatedDateRange();

        $this->form->fill($state);

        Notification::make()
            ->title('Report updated')
            ->success()
            ->send();
    }

    public function exportCsv(): StreamedResponse
    {
        $state = $this->validatedDateRange();
        $orders = $this->ordersQuery($state['date_from'], $state['date_to'])->get();

        $filename = sprintf(
            'othbar-orders-%s-to-%s.csv',
            Carbon::parse($state['date_from'])->format('Y-m-d'),
            Carbon::parse($state['date_to'])->format('Y-m-d'),
        );

        return response()->streamDownload(function () use ($orders): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'Order number',
                'Date',
                'Customer',
                'Phone',
                'Fulfillment',
                'Items',
                'Subtotal (Nu.)',
                'Discount (Nu.)',
                'GST (Nu.)',
                'Total (Nu.)',
                'Payment status',
                'Order status',
            ]);

            foreach ($orders as $order) {
                $order->loadMissing(['items', 'shippingAddress']);
                $pricing = $order->pricingSummary();

                fputcsv($handle, [
                    $order->number,
                    $order->created_at?->format('Y-m-d H:i'),
                    $order->shippingAddress?->full_name,
                    $order->shippingAddress?->phone,
                    $order->isPickup() ? 'In-store pickup' : 'Delivery',
                    $order->items->sum('quantity'),
                    number_format($pricing['subtotal_minor'] / 100, 2, '.', ''),
                    number_format($pricing['discount_minor'] / 100, 2, '.', ''),
                    number_format($pricing['gst_minor'] / 100, 2, '.', ''),
                    number_format($order->total_minor / 100, 2, '.', ''),
                    $order->payment_status->getLabel(),
                    $order->status->getLabel(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function reportViewData(): array
    {
        $state = $this->reportDateRange();
        $orders = $this->ordersQuery($state['date_from'], $state['date_to'])->get();
        $totalMinor = (int) $orders->sum('total_minor');

        return [
            'orders' => $orders,
            'dateFrom' => $state['date_from'],
            'dateTo' => $state['date_to'],
            'orderCount' => $orders->count(),
            'totalMinor' => $totalMinor,
        ];
    }

    /**
     * @return array{date_from: string, date_to: string}
     */
    protected function validatedDateRange(): array
    {
        $state = $this->reportDateRange();

        $this->form->fill($state);

        return $state;
    }

    /**
     * @return Builder<Order>
     */
    protected function ordersQuery(string $from, string $to)
    {
        return Order::query()
            ->successful()
            ->createdBetween($from, $to)
            ->with(['items', 'shippingAddress'])
            ->orderByDesc('created_at');
    }
}
