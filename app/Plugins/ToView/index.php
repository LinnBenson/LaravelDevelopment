<?php

use App\Providers\PluginProvider;
use App\Plugins\ToView\Support\BootstrapIcons;
use App\Plugins\ToView\Controllers\AssetController;

/**
 * To View Plugin
 */
return new class extends PluginProvider {
    /**
     * 构建程序信息
     */
    public function __construct() {
        $this->name = 'To View';
        $this->description = '视图渲染组件';
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
        $this->hook( 'ADMIN_PANEL_PROVIDER_PANEL', 'registerAdminPage' );
    }
    /**
     * 注册基本信息
     * @return void
     */
    public function register(): void {
        app( 'view' )->addNamespace( 'View', "{$this->path}Views" );
        Route::middleware( ['web'] )
            ->prefix( 'plugin_toview' )
            ->name( 'plugin.toview.' )
            ->group( function(): void {
                Route::get( '/assets/{path}', [AssetController::class, 'show'] )
                    ->where( 'path', '.*' )
                    ->name( 'asset' );
            } );
    }
    /**
     * 注入后台管理界面
     * @param $panel
     * @return void
     */
    public function registerAdminPage( $panel ): void {
        $panel->pages( [
            BootstrapIcons::class,
        ] );
    }
};