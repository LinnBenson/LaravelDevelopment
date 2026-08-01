<?php

use App\Plugins\LaravelTool\Controllers\AdminController;
use App\Plugins\LaravelTool\Middleware\EnsureAdminLevel;
use App\Providers\PluginProvider;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

/**
 * Readme Plugin
 */
return new class extends PluginProvider {
    /**
     * 构建程序信息
     */
    public function __construct() {
        $this->name = 'Laravel Tool';
        $this->description = 'Laravel Framework Development Tools';
        $this->version = '1.0.0';
        $this->author = 'System';
        $this->setType( 1 );
    }
    /**
     * 插件启动时执行的操作
     * @return void
     */
    public function boot(): void {
        $this->hook( 'APP_SERVICE_PROVIDER_BOOT', 'register' );
    }
    /**
     * 注册基本信息
     * @return void
     */
    public function register(): void {
        Route::post( '/admin/plugins/laravel-tool/run-command', [AdminController::class, 'runCommand'] )
            ->middleware( ['web', Authenticate::class, EnsureAdminLevel::class.':9000'] )
            ->name( 'plugins.laravel-tool.run-command' );
    }
};
