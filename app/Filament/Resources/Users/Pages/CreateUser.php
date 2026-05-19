<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['role']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $role = $this->form->getState()['role'] ?? 'staff';
        $this->record->syncRoles([$role]);
    }
}
