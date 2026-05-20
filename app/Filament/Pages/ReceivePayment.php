<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\PaymentAllocationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ReceivePayment extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Receive payment';

    protected static ?string $title = 'Receive payment';

    protected static ?string $slug = 'receive-payment';

    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('payments.receive');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Record cash or bank payments against one invoice, or spread automatically across outstanding invoices.';
    }

    public function mount(): void
    {
        $customerId = request()->integer('customer_id') ?: null;
        $invoiceId = request()->integer('invoice_id') ?: null;

        $fill = [
            'apply_mode' => 'invoice',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ];

        if ($customerId) {
            $fill['customer_id'] = $customerId;
        }

        if ($invoiceId) {
            $invoiceQuery = Invoice::query()->where('id', $invoiceId);
            if ($customerId) {
                $invoiceQuery->where('customer_id', $customerId);
            }
            $invoice = $invoiceQuery->first();

            if ($invoice !== null) {
                $fill['customer_id'] = $invoice->customer_id;
                $fill['apply_mode'] = 'invoice';
                $fill['invoice_id'] = $invoice->id;

                if ($invoice->balanceDueMinor() > 0) {
                    $fill['amount_minor'] = $invoice->balanceDueMinor();
                }
            }
        }

        $this->form->fill($fill);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Customer')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(fn () => Customer::query()
                                ->where('is_active', true)
                                ->orderBy('display_name')
                                ->pluck('display_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('invoice_id', null);
                                $set('amount_minor', null);
                            })
                            ->createOptionForm(CustomerForm::createOptionFields())
                            ->createOptionModalHeading('New customer')
                            ->createOptionAction(
                                fn ($action) => $action->visible(
                                    fn (): bool => Auth::user()?->can('customers.manage')
                                        || Auth::user()?->can('payments.receive')
                                        ?? false,
                                ),
                            ),
                        Placeholder::make('outstanding_summary')
                            ->label('Outstanding')
                            ->content(function (Get $get): string {
                                $customerId = $get('customer_id');
                                if (! $customerId) {
                                    return 'Select a customer to see open invoices.';
                                }

                                $count = Invoice::query()
                                    ->where('customer_id', $customerId)
                                    ->whereColumn('amount_paid_minor', '<', 'total_minor')
                                    ->where('status', '!=', 'void')
                                    ->count();

                                if ($count === 0) {
                                    return 'No outstanding invoices for this customer.';
                                }

                                $total = app(PaymentAllocationService::class)
                                    ->customerOutstandingMinor((int) $customerId);

                                return $count.' open invoice'.($count === 1 ? '' : 's')
                                    .' — total due Nu. '.number_format($total / 100, 2);
                            })
                            ->visible(fn (Get $get): bool => filled($get('customer_id'))),
                    ]),
                Section::make('How to apply')
                    ->description('Choose one invoice or let the system pay oldest invoices first.')
                    ->schema([
                        Select::make('apply_mode')
                            ->label('Apply payment')
                            ->options([
                                'invoice' => 'Pay a specific invoice',
                                'auto' => 'Split across outstanding invoices (oldest first)',
                            ])
                            ->default('invoice')
                            ->required()
                            ->live(),
                        Select::make('invoice_id')
                            ->label('Invoice')
                            ->options(function (Get $get): array {
                                $customerId = $get('customer_id');
                                if (! $customerId) {
                                    return [];
                                }

                                return Invoice::query()
                                    ->where('customer_id', $customerId)
                                    ->where('status', '!=', 'void')
                                    ->whereColumn('amount_paid_minor', '<', 'total_minor')
                                    ->orderByDesc('issue_date')
                                    ->get()
                                    ->mapWithKeys(fn (Invoice $inv) => [
                                        $inv->id => "{$inv->number} — due Nu. ".number_format($inv->balanceDueMinor() / 100, 2)
                                            .($inv->due_date ? ' (due '.$inv->due_date->format('d M Y').')' : ''),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->required(fn (Get $get): bool => $get('apply_mode') === 'invoice')
                            ->visible(fn (Get $get): bool => $get('apply_mode') === 'invoice')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $invoice = Invoice::query()->find($state);
                                if ($invoice !== null && $invoice->balanceDueMinor() > 0) {
                                    $set('amount_minor', $invoice->balanceDueMinor());
                                }
                            }),
                        Placeholder::make('auto_help')
                            ->label('')
                            ->content('The full payment amount will be applied to this customer\'s open invoices, starting with the oldest due date. Any leftover stays unallocated on the receipt.')
                            ->visible(fn (Get $get): bool => $get('apply_mode') === 'auto'),
                    ]),
                Section::make('Payment details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount_minor')
                            ->label('Amount received (Nu.)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->helperText(fn (Get $get): ?string => match ($get('apply_mode')) {
                                'invoice' => 'Usually the invoice balance due. You can enter a partial payment.',
                                'auto' => 'Applied to open invoices from oldest to newest.',
                                default => null,
                            })
                            ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? ((int) $state) / 100 : null)
                            ->dehydrateStateUsing(fn ($state) => (int) round((float) $state * 100)),
                        DatePicker::make('payment_date')
                            ->label('Payment date')
                            ->required()
                            ->default(now()),
                        Select::make('payment_method')
                            ->label('Payment method')
                            ->options([
                                'cash' => 'Cash',
                                'bank-transfer' => 'Bank transfer',
                            ])
                            ->required()
                            ->live(),
                        Select::make('bank_account_id')
                            ->label('Bank account')
                            ->options(BankAccount::query()->where('is_active', true)->get()->mapWithKeys(
                                fn (BankAccount $a) => [$a->id => $a->displayLabel()],
                            ))
                            ->visible(fn (Get $get) => $get('payment_method') === 'bank-transfer'),
                        TextInput::make('reference')
                            ->label('Reference / journal no.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Internal notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $service = app(PaymentAllocationService::class);
        $amountMinor = (int) $data['amount_minor'];

        if ($data['apply_mode'] === 'invoice') {
            $invoice = Invoice::query()->findOrFail((int) $data['invoice_id']);

            if ($invoice->customer_id !== (int) $data['customer_id']) {
                throw ValidationException::withMessages([
                    'invoice_id' => 'This invoice does not belong to the selected customer.',
                ]);
            }

            if ($amountMinor > $invoice->balanceDueMinor()) {
                throw ValidationException::withMessages([
                    'amount_minor' => 'Amount cannot exceed the invoice balance due (Nu. '
                        .number_format($invoice->balanceDueMinor() / 100, 2).').',
                ]);
            }

            $payment = $service->receivePayment(
                (int) $data['customer_id'],
                $amountMinor,
                $data['payment_method'],
                [['invoice_id' => $invoice->id, 'amount_minor' => $amountMinor]],
                Auth::user(),
                $data['payment_date'] ?? null,
                isset($data['bank_account_id']) ? (int) $data['bank_account_id'] : null,
                $data['reference'] ?? null,
                $data['notes'] ?? null,
            );
        } else {
            $payment = $service->receivePaymentAutoAllocate(
                (int) $data['customer_id'],
                $amountMinor,
                $data['payment_method'],
                Auth::user(),
                $data['payment_date'] ?? null,
                isset($data['bank_account_id']) ? (int) $data['bank_account_id'] : null,
                $data['reference'] ?? null,
                $data['notes'] ?? null,
            );
        }

        $allocated = $payment->allocations->sum('amount_minor');
        $message = "Payment {$payment->number} recorded";

        if ($allocated < $amountMinor) {
            $message .= ' (Nu. '.number_format(($amountMinor - $allocated) / 100, 2).' unallocated)';
        }

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->redirect(route('filament.admin.payments.receipt', [
            'payment' => $payment,
            'autoprint' => 1,
        ]));
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->livewireSubmitHandler('submit')
                ->footer([
                    Actions::make([
                        Action::make('submit')
                            ->label('Record payment')
                            ->submit('submit'),
                    ]),
                ]),
        ]);
    }
}
