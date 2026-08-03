<?php

namespace App\Filament\Concerns;

use App\Models\AdminUser;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;

/**
 * 禁用管理员会话中间件
 * 在 Filament 身份认证前自动注销已被禁用的后台管理员。
 * @package App\Filament\Concerns
 */
class LogoutDisabledAdmin extends Authenticate {
    /**
     * 检查管理员状态
     * 清理已禁用管理员的登录会话，再交由 Filament 跳转到登录页。
     * @param mixed $request 请求对象
     * @param array<string> $guards 认证守卫列表
     * @return void
     */
    protected function authenticate( $request, array $guards ): void {
        $guard = Filament::auth();
        $adminUser = $guard->user();
        if ( $adminUser instanceof AdminUser && !$adminUser->status ) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flash( 'status', '当前管理员账号已被禁用，请使用其他账号登录。' );
        }
        parent::authenticate( $request, $guards );
    }
}
