<?php

namespace App\Filament\Schemas;

use App\Enums\BusinessType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessSettingsForm
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
        return [
            Section::make('Business information (DRC compliance)')
                ->description('Required for Bhutan GST compliance with the Department of Revenue and Customs.')
                ->schema([
                    Select::make('business_type')
                        ->label('Business type')
                        ->options(collect(BusinessType::cases())->mapWithKeys(
                            fn (BusinessType $type) => [$type->value => $type->label()],
                        ))
                        ->nullable(),
                    TextInput::make('business_name')
                        ->label('Registered business name')
                        ->required()
                        ->maxLength(200),
                    TextInput::make('drc_registration_number')
                        ->label('DRC registration number')
                        ->maxLength(50)
                        ->helperText('Optional — obtain from DRC if registered.'),
                    TextInput::make('gst_tpn')
                        ->label('GST TPN')
                        ->required()
                        ->maxLength(50),
                    TextInput::make('business_licence_number')
                        ->label('Business licence number')
                        ->maxLength(80),
                    TextInput::make('business_address_line1')
                        ->label('Address line 1')
                        ->maxLength(255),
                    TextInput::make('business_address_line2')
                        ->label('Address line 2')
                        ->maxLength(255),
                    TextInput::make('business_city')
                        ->maxLength(100),
                    TextInput::make('business_district')
                        ->label('District')
                        ->maxLength(100),
                    TextInput::make('business_postal_code')
                        ->label('Postal code')
                        ->maxLength(20),
                    TextInput::make('business_phone')
                        ->tel()
                        ->maxLength(50),
                    TextInput::make('business_email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('business_website')
                        ->url()
                        ->maxLength(255),
                    FileUpload::make('business_logo_path')
                        ->label('Business logo')
                        ->disk('public')
                        ->directory('business')
                        ->image()
                        ->maxSize(2048)
                        ->helperText('PNG, JPG, or SVG. Max 2MB.'),
                ])
                ->columns(2),
            Section::make('Invoice settings')
                ->schema([
                    TextInput::make('default_currency')
                        ->default('BTN')
                        ->maxLength(8)
                        ->required(),
                    Select::make('fiscal_year_start_month')
                        ->label('Fiscal year starts')
                        ->options(collect(range(1, 12))->mapWithKeys(
                            fn (int $m) => [$m => date('F', mktime(0, 0, 0, $m, 1))],
                        ))
                        ->required(),
                    TextInput::make('invoice_payment_terms_days')
                        ->label('Payment terms (days)')
                        ->numeric()
                        ->minValue(0)
                        ->default(30)
                        ->required(),
                    Textarea::make('invoice_terms_text')
                        ->label('Default invoice terms')
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('invoice_footer_text')
                        ->label('Invoice footer')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
