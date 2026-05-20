<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'My profile';

    public static function getLabel(): string
    {
        return 'My profile';
    }

    public static function isSimple(): bool
    {
        return false;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'View your account details and update your name, email, or password.';
    }

    public function form(Schema $schema): Schema
    {
        $user = $this->getUser();
        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->join(', ')
            : '—';

        return $schema
            ->components([
                Section::make('Account overview')
                    ->description('Details for your signed-in account.')
                    ->schema([
                        Placeholder::make('email_display')
                            ->label('Email')
                            ->content(fn (): string => (string) $user->email),
                        Placeholder::make('role_display')
                            ->label('Role')
                            ->content($roles !== '' ? $roles : '—'),
                    ])
                    ->columns(2),
                Section::make('Update profile')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ])
                    ->columns(2),
                Section::make('Change password')
                    ->description('Leave blank to keep your current password.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return collect($data)
            ->only(['name', 'email', 'password'])
            ->filter(fn (mixed $value, string $key): bool => $key !== 'password' || filled($value))
            ->all();
    }
}
