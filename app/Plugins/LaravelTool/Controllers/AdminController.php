<?php

namespace App\Plugins\LaravelTool\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Throwable;

class AdminController extends Controller {
    /** @var array<int, string> 需要名称参数的生成命令 */
    private const GENERATOR_COMMANDS = [
        'make-service',
        'make-controller',
        'make-model',
        'make-command',
        'make-event',
        'make-factory',
        'make-job',
        'make-listener',
        'make-mail',
        'make-middleware',
        'make-notification',
        'make-policy',
        'make-provider',
        'make-request',
        'make-resource',
        'make-rule',
        'make-seeder',
        'make-test',
    ];

    /** @var array<string, string> 允许通过管理页面运行的命令 */
    private const COMMANDS = [
        'environment' => 'env',
        'clear-cache' => 'optimize:clear',
        'optimize' => 'optimize',
        'application-cache-clear' => 'cache:clear',
        'config-cache' => 'config:cache',
        'config-clear' => 'config:clear',
        'event-cache' => 'event:cache',
        'event-clear' => 'event:clear',
        'event-list' => 'event:list',
        'route-cache' => 'route:cache',
        'route-clear' => 'route:clear',
        'route-list' => 'route:list',
        'view-cache' => 'view:cache',
        'view-clear' => 'view:clear',
        'migrate-status' => 'migrate:status',
        'database-show' => 'db:show',
        'schedule-list' => 'schedule:list',
        'queue-failed' => 'queue:failed',
        'queue-restart' => 'queue:restart',
        'make-service' => 'make:class',
        'make-controller' => 'make:controller',
        'make-model' => 'make:model',
        'make-command' => 'make:command',
        'make-event' => 'make:event',
        'make-factory' => 'make:factory',
        'make-job' => 'make:job',
        'make-listener' => 'make:listener',
        'make-mail' => 'make:mail',
        'make-middleware' => 'make:middleware',
        'make-migration' => 'make:migration',
        'make-notification' => 'make:notification',
        'make-policy' => 'make:policy',
        'make-provider' => 'make:provider',
        'make-request' => 'make:request',
        'make-resource' => 'make:resource',
        'make-rule' => 'make:rule',
        'make-seeder' => 'make:seeder',
        'make-test' => 'make:test',
    ];

    /**
     * 运行命令
     * 校验命令标识并执行白名单内的 Artisan 命令。
     * @param Request $request 请求对象
     * @return JsonResponse JSON 响应
     */
    public function runCommand( Request $request ): JsonResponse {
        $validated = $request->validate( [
            'command' => ['required', 'string', Rule::in( array_keys( self::COMMANDS ) )],
        ] );
        $command = self::COMMANDS[$validated['command']];
        $parameters = [];
        if ( $validated['command'] === 'make-migration' ) {
            $nameData = $request->validate( [
                'name' => ['required', 'string', 'max:160', 'regex:/\A[a-z][a-z0-9_]*\z/'],
            ], [
                'name.required' => '请填写迁移名称。',
                'name.regex' => '迁移名称只能使用小写字母、数字和下划线。',
            ] );
            $parameters['name'] = $nameData['name'];
        }elseif ( in_array( $validated['command'], self::GENERATOR_COMMANDS, true ) ) {
            $nameData = $request->validate( [
                'name' => ['required', 'string', 'max:160', 'regex:/\A[A-Z][A-Za-z0-9]*(?:[\\\\\/][A-Z][A-Za-z0-9]*)*\z/'],
            ], [
                'name.required' => '请填写名称。',
                'name.regex' => '名称必须使用大驼峰格式，可包含命名空间。',
            ] );
            $name = str_replace( '/', '\\', $nameData['name'] );
            if ( $validated['command'] === 'make-service' ) {
                $name = "Services\\{$name}";
            }
            $parameters['name'] = $name;
        }
        try {
            $exitCode = Artisan::call( $command, $parameters );
            $output = trim( Artisan::output() );
            if ( $exitCode !== 0 ) {
                return echoJson( false, [
                    'message' => '命令执行失败。',
                    'output' => $output,
                ], 500 );
            }
            return echoJson( true, [
                'message' => '命令执行成功。',
                'output' => $output,
            ] );
        }catch ( Throwable $throwable ) {
            report( $throwable );
            return echoJson( false, [
                'message' => '命令执行异常，请查看系统日志。',
                'output' => '',
            ], 500 );
        }
    }
}
