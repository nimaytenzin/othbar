<?php

namespace App\Filament\Resources\TaxClassifications\Pages;

use App\Filament\Resources\TaxClassifications\TaxClassificationResource;
use Database\Seeders\TaxClassificationSeeder;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListTaxClassifications extends ListRecords
{
    protected static string $resource = TaxClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_defaults')
                ->label('Reset to defaults')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Restore or update the three Bhutan DRC default classifications (Standard, Zero-Rated, Exempt). Custom classifications are not removed.')
                ->action(function (): void {
                    (new TaxClassificationSeeder)->run();

                    Notification::make()
                        ->title('Default tax classifications restored')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
