<?php

namespace App\Filament\Schemas;

use App\Filament\Resources\TaxClassifications\TaxClassificationResource;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TaxSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(static::components());
    }

    /**
     * @return array<int, Component>
     */
    public static function components(): array
    {
        $classificationsUrl = TaxClassificationResource::getUrl('index');

        return [
            Section::make('GST configuration')
                ->description('Configure GST registration and default product tax treatment per DRC guidelines.')
                ->schema([
                    Toggle::make('is_gst_registered')
                        ->label('GST registered with DRC')
                        ->default(true),
                    Select::make('default_tax_classification_id')
                        ->label('Default tax classification for new products')
                        ->relationship(
                            'defaultTaxClassification',
                            'name',
                            fn ($query) => $query->where('is_active', true)->where('code', '!=', 'EXEMPT'),
                        )
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Applied when a product has no classification set. Exempt supplies should be marked per product.'),
                    Placeholder::make('tax_classifications_link')
                        ->label('Tax classifications')
                        ->content(fn (): HtmlString => new HtmlString(
                            'Manage rates and types (Standard 5%, Zero-Rated, Exempt, and custom). '
                            ."<a href=\"{$classificationsUrl}\" class=\"underline font-medium\">Open tax classifications</a>"
                        ))
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('About Bhutan GST')
                ->schema([
                    Placeholder::make('gst_help')
                        ->hiddenLabel()
                        ->content(fn (): HtmlString => new HtmlString(
                            '<ul class="list-disc ps-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">'
                            .'<li><strong>Standard (5%):</strong> Most goods and services — GST charged, input credits claimable</li>'
                            .'<li><strong>Zero-Rated (0%):</strong> Exports and special supplies — 0% GST, input credits claimable</li>'
                            .'<li><strong>Exempt:</strong> Financial, healthcare, education, etc. — no GST, no input credits</li>'
                            .'</ul>'
                        )),
                ]),
            Section::make('Document prefixes')
                ->description('Applies to new documents only. Letters, numbers, hyphens (max 20).')
                ->schema([
                    TextInput::make('prefix_invoice')->label('Invoice')->maxLength(20)->required(),
                    TextInput::make('prefix_customer_payment')->label('Customer payment receipt')->maxLength(20)->required(),
                    TextInput::make('prefix_bill')->label('Bill')->maxLength(20),
                    TextInput::make('prefix_supplier_payment')->label('Supplier payment')->maxLength(20),
                    TextInput::make('prefix_quotation')->label('Quotation')->maxLength(20),
                    TextInput::make('prefix_contract')->label('Contract')->maxLength(20),
                    TextInput::make('prefix_credit_note')->label('Credit note')->maxLength(20),
                    TextInput::make('prefix_debit_note')->label('Debit note')->maxLength(20),
                ])
                ->columns(2),
        ];
    }
}
