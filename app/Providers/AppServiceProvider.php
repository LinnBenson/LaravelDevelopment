<?php

namespace App\Providers;

use App\Providers\PluginProvider;
use App\Filament\Resources\AdminControl\AdminUsers\AdminUserPolicy;
use App\Filament\Resources\UserManagement\Users\UserPolicy;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * 注册应用服务。
     */
    public function register(): void {
        $this->mergeConfigFrom( app_path( 'Filament/Config/system_uploads.php' ), 'system_uploads' );

        PluginProvider::runHook( 'APP_SERVICE_PROVIDER_REGISTER' );
    }

    /**
     * 启动应用服务。
     */
    public function boot(): void {
        Gate::policy( AdminUser::class, AdminUserPolicy::class );
        Gate::policy( User::class, UserPolicy::class );

        PluginProvider::runHook( 'APP_SERVICE_PROVIDER_BOOT' );
    }
}
