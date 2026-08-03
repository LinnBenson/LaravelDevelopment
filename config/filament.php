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
    // 代理管理等级，小于等于此等级即为代理
    'agent' => 100,
    // 后台菜单等级权限，访问需要大于此级别
    'navigation_levels' => [
        'dashboard' => 0,
        'admin_users' => 0,
        'plugin_management' => 99899,
        'users' => 0,
        'system_config' => 98999,
        'service_management' => 99899,
        'log_information' => 99899,
        'filament_icons' => 99899,
        'readme' => 99899,
    ],
];
