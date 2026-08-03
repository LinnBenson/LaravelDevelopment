<?php

namespace App\Filament\Concerns;

use App\Models\AdminUser;
use Closure;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理员等级中间件
 * 只允许超过指定等级的后台管理员继续访问插件路由。
 */
class AdminLevel {
    public function __construct(
        private readonly Pipeline $pipeline,
        private readonly Router $router,
    ) {}
    /**
     * 检查管理员等级
     * 自动补齐插件后台路由所需的 Web 与 Filament 登录中间件。
     * @param Request $request 请求对象
     * @param Closure $next 后续处理
     * @param int|string|null $level 最低等级边界
     * @return Response HTTP 响应
     */
    public function handle( Request $request, Closure $next, int|string|null $level = null ): Response {
        if ( is_null( $level ) ) {
            $level = config( 'filament.navigation_levels.plugin_management', 99900 );
        }
        $middleware = [Authenticate::class];
        if ( !$this->routeUsesWebMiddleware( $request ) ) {
            $middleware = [...$this->router->getMiddlewareGroups()['web'], ...$middleware];
        }
        return $this->pipeline
            ->send( $request )
            ->through( $middleware )
            ->then( function( Request $request ) use ( $next, $level ): Response {
                $adminUser = auth( 'admin' )->user();
                abort_unless(
                    $adminUser instanceof AdminUser && $adminUser->level > (int) $level,
                    403,
                );
                return $next( $request );
            } );
    }
    /**
     * 判断当前路由是否已经使用 Web 中间件组
     * 避免后台面板路由重复启动 Session 和执行 CSRF 校验。
     * @param Request $request 请求对象
     * @return bool 是否已使用 Web 中间件组
     */
    private function routeUsesWebMiddleware( Request $request ): bool {
        $route = $request->route();
        if ( !is_object( $route ) || !method_exists( $route, 'gatherMiddleware' ) ) { return false; }
        return in_array( 'web', $route->gatherMiddleware(), true );
    }
    /**
     * 检查管理员等级
     * @param Request $request 请求对象
     * @param int|string $level 最低等级边界
     * @return bool 是否允许访问
     */
    public static function check( Request $request, int|string $level ): bool {
        $adminUser = auth( 'admin' )->user();
        return $adminUser instanceof AdminUser && $adminUser->level > (int) $level;
    }
}
