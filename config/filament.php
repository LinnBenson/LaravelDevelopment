<?php
/**
 * Filament 配置文件
 * @package config
 */
return [
    // 后台面板路径
    'path' => env( 'APP_ADMIN_PREFIX', 'admin' ),
    // 后台资源路径
    'assets_path' => 'filament',
    // 代理管理等级
    'agent' => 100,
    // 后台菜单等级权限
    'navigation_levels' => [
        'dashboard' => 0,
        'admin_users' => 0,
        'plugin_management' => 9000,
        'users' => 0,
        'system_config' => 9000,
        'service_management' => 9000,
        'log_information' => 9000,
        'filament_icons' => 9000,
        'readme' => 9000,
    ],
];
