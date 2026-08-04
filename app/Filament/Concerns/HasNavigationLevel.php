<?php

namespace App\Filament\Concerns;

use App\Models\AdminUser;
use Filament\Facades\Filament;

/**
 * 后台菜单等级权限
 * 根据集中配置控制菜单显示和页面访问。
 */
trait HasNavigationLevel {
    /**
     * 判断是否可以访问后台页面。
     * 同时保留页面或资源原有的访问权限判断。
     * @return bool 是否允许访问
     */
    public static function canAccess(): bool {
        return parent::canAccess() && static::hasRequiredNavigationLevel();
    }

    /**
     * 判断是否注册后台菜单。
     * @return bool 是否显示菜单
     */
    public static function shouldRegisterNavigation(): bool {
        return parent::shouldRegisterNavigation() && static::hasRequiredNavigationLevel();
    }

    /**
     * 判断当前管理员是否达到菜单要求等级。
     * @return bool 是否达到要求等级
     */
    private static function hasRequiredNavigationLevel(): bool {
        $adminUser = Filament::auth()->user();
        if ( !$adminUser instanceof AdminUser ) { return false; }
        $levels = config( 'filament.navigation_levels', [] );
        $minimumLevel = is_array( $levels ) ? (int) ( $levels[static::$navigationPermission] ?? 0 ) : 0;
        return $adminUser->level > max( $minimumLevel, 0 );
    }
}
