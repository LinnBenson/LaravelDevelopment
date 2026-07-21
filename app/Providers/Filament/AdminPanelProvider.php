<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AdminControl\PluginManagement\PluginManagement;
use App\Filament\Resources\Dashboard\Login\Login;
use App\Filament\Resources\Dashboard\Home\HomeDashboard;
use App\Filament\Resources\DeveloperCenter\BootstrapIcons\BootstrapIcons;
use App\Filament\Resources\DeveloperCenter\LogInformation\LogInformation;
use App\Filament\Resources\DeveloperCenter\Readme\Readme;
use App\Filament\Resources\DeveloperCenter\FilamentIcons\FilamentIcons;
use App\Filament\Resources\SystemSettings\SystemConfig\SystemConfigPage;
use App\Filament\Resources\SystemSettings\ServiceManagement\ServiceManagement;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Providers\PluginProvider;

/**
 * AdminPanelProvider
 * Filament 后台面板服务提供器。
 * @package App\Providers\Filament
 */
class AdminPanelProvider extends PanelProvider {
    /**
     * 配置后台面板。
     * 配置管理员后台面板路径、登录守卫、资源发现和中间件。
     * @param Panel $panel Filament 面板
     * @return Panel Filament 面板
     */
    public function panel( Panel $panel ): Panel {
        $panel
            ->default()
            ->id( 'admin' )
            ->path( env( 'APP_ADMIN_PREFIX', 'admin' ) )
            ->authGuard( 'admin' )
            ->login( Login::class )
            ->colors( [
                'primary' => Color::Amber,
            ] )
            ->discoverResources( in: app_path( 'Filament/Resources' ), for: 'App\Filament\Resources' )
            ->pages( [
                HomeDashboard::class,
                PluginManagement::class,
                BootstrapIcons::class,
                FilamentIcons::class,
                LogInformation::class,
                Readme::class,
                SystemConfigPage::class,
                ServiceManagement::class,
            ] )
            ->navigationGroups( [
                '管理员控制',
                '用户管理',
                '系统设置',
                '开发者中心',
            ] )
            ->discoverWidgets( in: app_path( 'Filament/Widgets' ), for: 'App\Filament\Widgets' )
            ->middleware( [
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ] )
            ->authMiddleware( [
                Authenticate::class,
            ] );
        PluginProvider::runHook( 'ADMIN_PANEL_PROVIDER_PANEL', $panel );
        return $panel;
    }
}
