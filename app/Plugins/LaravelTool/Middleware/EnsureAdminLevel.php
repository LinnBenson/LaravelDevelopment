<?php

namespace App\Plugins\LaravelTool\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理员等级中间件
 * 只允许超过指定等级的后台管理员继续访问插件路由。
 */
class EnsureAdminLevel {
    /**
     * 检查管理员等级
     * @param Request $request 请求对象
     * @param Closure $next 后续处理
     * @param int|string $level 最低等级边界
     * @return Response HTTP 响应
     */
    public function handle( Request $request, Closure $next, int|string $level ): Response {
        $adminUser = auth( 'admin' )->user();
        abort_unless(
            $adminUser instanceof AdminUser && $adminUser->level > (int) $level,
            403,
        );
        return $next( $request );
    }
}
