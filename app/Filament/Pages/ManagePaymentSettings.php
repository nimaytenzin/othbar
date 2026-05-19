<?php

namespace App\Filament\Pages;

use App\Filament\Schemas\PaymentSettingsForm;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Throwable;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManagePaymentSettings extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Payment & GST';

    protected static ?string $title = 'Payment & GST';

    protected static ?string $slug = 'payment-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Store';

    protected static ?int $navigationSort = 2;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    #[Locked]
    public SiteSetting $settings;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('settings.manage');
    }

    public function mount(): void
    {
        $this->settings = SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            SiteSetting::defaults()
        );

        $this->fillForm();
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Payment & GST';
    }

    protected function fillForm(): void
    {
        $this->form->fill(
            $this->settings->only([
                'gst_percentage',
                'payment_merchant_account',
            ])
        );
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            $this->settings->update($data);

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Payment settings saved')
            ->success()
            ->send();

        $this->fillForm();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->model($this->settings)
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return PaymentSettingsForm::configure($schema);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label('Save payment settings')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ])
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->sticky($this->areFormActionsSticky())
                    ->key('form-actions'),
            ]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
