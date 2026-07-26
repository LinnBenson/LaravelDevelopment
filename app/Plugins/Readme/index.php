<?php

use App\Providers\PluginProvider;

/**
 * Readme Plugin
 */
return new class extends PluginProvider {
    /**
     * 构建程序信息
     */
    public function __construct() {
        $this->name = 'Readme';
        $this->description = '这是一个示例的插件程序，展示了插件的基本结构，可直接删除！';
        $this->version = '1.0.0';
        $this->author = 'System';
        $this->setType( 1 );
    }
    /**
     * 插件启动时执行的操作
     * @return void
     */
    public function boot(): void {
        $this->hook( 'APP_SERVICE_PROVIDER_REGISTER', function() {});
        $this->hook( 'APP_SERVICE_PROVIDER_BOOT', function() {});
        $this->hook( 'ADMIN_PANEL_PROVIDER_PANEL', function() {});
        $this->hook( 'SET_REQUEST_MIDDLEWARE_HANDLE', function() {});
    }
};