<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\CreateCounterOrder;
use App\Http\Controllers\Admin\InvoicePrintController;
use App\Http\Controllers\Admin\OrderPaymentProofController;
use App\Http\Controllers\Admin\OrderReceiptController;
use App\Http\Controllers\Admin\PaymentReceiptController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        $cssPath = public_path('css/oth-order-view.css');
        $version = file_exists($cssPath) ? (string) filemtime($cssPath) : '1';

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => Blade::render(
                '<link rel="stylesheet" href="{{ $href }}" data-navigate-track />',
                ['href' => asset('css/oth-order-view.css').'?v='.$version],
            ),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): string => view('filament.auth.home-link')->render(),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class, isSimple: false)
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('My profile'),
            ])
            ->homeUrl(function (): string {
                $user = auth()->user();

                if ($user !== null && $user->hasRole('staff') && ! $user->hasRole('administrator')) {
                    return CreateCounterOrder::getUrl();
                }

                return Dashboard::getUrl();
            })
            ->brandName('Othbar')
            ->colors([
                'primary' => Color::hex('#1E3A2A'),
                'gray' => Color::Zinc,
                'success' => Color::hex('#2D5440'),
                'warning' => Color::hex('#C4843C'),
            ])
            ->font('Jost')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Store'),
                NavigationGroup::make('Sales'),
                NavigationGroup::make('Payments'),
                NavigationGroup::make('Catalog'),
                NavigationGroup::make('Products & Inventory'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Administration'),
                NavigationGroup::make('Settings'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authenticatedRoutes(function (): void {
                Route::get('/orders/{order}/receipt', [OrderReceiptController::class, 'show'])
                    ->name('orders.receipt');
                Route::get('/orders/{order}/payment-proof', [OrderPaymentProofController::class, 'download'])
                    ->name('orders.payment-proof');
                Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'show'])
                    ->name('invoices.print');
                Route::get('/invoices/{invoice}/pdf', [InvoicePrintController::class, 'downloadPdf'])
                    ->name('invoices.pdf');
                Route::get('/payments/{payment}/receipt', [PaymentReceiptController::class, 'show'])
                    ->name('payments.receipt');
            });
    }
}
