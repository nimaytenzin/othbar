<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Invoices\Support\InvoiceLinePreview;
use App\Models\SiteSetting;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $settings = SiteSetting::current();
        $defaultDueDays = $settings->invoice_payment_terms_days ?? 30;

        return $schema->columns(1)->components([
            Section::make('Customer & dates')->columns(2)->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship(
                        name: 'customer',
                        titleAttribute: 'display_name',
                        modifyQueryUsing: fn ($query) => $query
                            ->where('is_active', true)
                            ->orderBy('display_name'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(CustomerForm::createOptionFields())
                    ->createOptionModalHeading('New customer')
                    ->createOptionAction(
                        fn ($action) => $action->visible(
                            fn (): bool => Auth::user()?->can('customers.manage')
                                || Auth::user()?->can('invoices.manage')
                                ?? false,
                        ),
                    ),
                DatePicker::make('issue_date')
                    ->label('Issue date')
                    ->required()
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) use ($defaultDueDays): void {
                        if ($state) {
                            $set('due_date', now()->parse($state)->addDays($defaultDueDays)->toDateString());
                        }
                    }),
                DatePicker::make('due_date')
                    ->label('Due date')
                    ->required()
                    ->default(now()->addDays($defaultDueDays)),
                Textarea::make('notes')->columnSpanFull(),
            ]),
            Section::make('Line items')
                ->description('Add products or custom lines. Totals update as you add items.')
                ->schema([
                    Hidden::make('lines')
                        ->default([])
                        ->live(),
                    TextInput::make('invoice_discount_minor')
                        ->label('Invoice discount (Nu.)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? ((int) $state) / 100 : null)
                        ->dehydrateStateUsing(fn ($state) => (int) round((float) ($state ?? 0) * 100)),
                    SchemaView::make('invoice_lines')
                        ->view('filament.invoices.lines-table')
                        ->viewData(function (Get $get): array {
                            $preview = InvoiceLinePreview::summarize(
                                $get('lines') ?? [],
                                (int) ($get('invoice_discount_minor') ?? 0),
                            );

                            return [
                                'rows' => $preview['rows'],
                                'totals' => [
                                    'subtotal_minor' => $preview['subtotal_minor'],
                                    'discount_minor' => $preview['discount_minor'],
                                    'tax_minor' => $preview['tax_minor'],
                                    'total_minor' => $preview['total_minor'],
                                ],
                            ];
                        }),
                ]),
        ]);
    }
}
