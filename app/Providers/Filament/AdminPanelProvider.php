<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->brandName('SAFE')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2.15rem')
            ->favicon(asset('images/favicon.png'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->defaultThemeMode(ThemeMode::Light)
            ->maxContentWidth(Width::ScreenTwoExtraLarge)
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.auth.login-shortcuts'),
            )
            ->colors([
                'primary' => [
                    50 => '#fff1f2',
                    100 => '#ffe4e6',
                    200 => '#fecdd3',
                    300 => '#fda4af',
                    400 => '#fb7185',
                    500 => '#e30613',
                    600 => '#c90010',
                    700 => '#a4000d',
                    800 => '#7f000a',
                    900 => '#4a0006',
                    950 => '#260003',
                ],
                'gray' => [
                    50 => '#fafafa',
                    100 => '#f4f4f5',
                    200 => '#e4e4e7',
                    300 => '#d4d4d8',
                    400 => '#a1a1aa',
                    500 => '#71717a',
                    600 => '#52525b',
                    700 => '#3f3f46',
                    800 => '#27272a',
                    900 => '#18181b',
                    950 => '#09090b',
                ],
            ])
            ->navigationGroups([
                'Operação escolar',
                'Comunicação',
                'Cadastros acadêmicos',
                'Administração',
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
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
