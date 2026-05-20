<?php

namespace App\Filament\Pages;

use App\Filament\Schemas\BusinessSettingsForm;
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
class ManageBusinessSettings extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Business';

    protected static ?string $title = 'Business settings';

    protected static ?string $slug = 'business-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    #[Locked]
    public SiteSetting $settings;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('settings.business') || $user->can('settings.manage'));
    }

    public function mount(): void
    {
        $this->settings = SiteSetting::query()->firstOrCreate(['id' => 1], SiteSetting::defaults());
        $this->fillForm();
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Business settings';
    }

    protected function fillForm(): void
    {
        $this->form->fill($this->settings->only([
            'business_type',
            'business_name',
            'drc_registration_number',
            'gst_tpn',
            'business_licence_number',
            'business_address_line1',
            'business_address_line2',
            'business_city',
            'business_district',
            'business_postal_code',
            'business_phone',
            'business_email',
            'business_website',
            'business_logo_path',
            'default_currency',
            'fiscal_year_start_month',
            'invoice_payment_terms_days',
            'invoice_terms_text',
            'invoice_footer_text',
        ]));
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();
            $this->settings->update($this->form->getState());
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

        Notification::make()->title('Business settings saved')->success()->send();
        $this->fillForm();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->operation('edit')->model($this->settings)->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return BusinessSettingsForm::configure($schema);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->label('Save business settings')->submit('save')->keyBindings(['mod+s']),
                ])->alignment($this->getFormActionsAlignment()),
            ]);
    }
}
