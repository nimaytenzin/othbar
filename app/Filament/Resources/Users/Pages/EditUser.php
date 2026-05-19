<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['role']);

        return $data;
    }

    protected function afterSave(): void
    {
        $role = $this->form->getState()['role'] ?? null;

        if ($role !== null) {
            $this->record->syncRoles([$role]);
        }
    }
}
