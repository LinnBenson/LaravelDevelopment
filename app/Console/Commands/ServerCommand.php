<?php

namespace App\Console\Commands;

use App\Workerman\Server;
use Illuminate\Console\Command;
use Throwable;

class ServerCommand extends Command {
    /**
     * 命令名称及签名
     * @var string
     */
    protected $signature = 'server
        {name : config/workerman.php 中的服务标识}
        {action : start、restart、stop 或 status}';

    /**
     * 命令描述内容
     * @var string
     */
    protected $description = '启动、重启、停止或查看指定 Workerman 服务状态';

    /**
     * 处理命令
     * @return int 命令退出码
     */
    public function handle(): int {
        global $argv;

        $name = (string) $this->argument( 'name' );
        $action = (string) $this->argument( 'action' );
        if ( !in_array( $action, ['start', 'restart', 'stop', 'status'], true ) ) {
            $this->error( '服务操作只能是 start、restart、stop 或 status。' );
            return self::FAILURE;
        }
        if ( preg_match( '/^[A-Za-z0-9_-]+$/', $name ) !== 1 ) {
            $this->error( '服务标识只能包含字母、数字、下划线和连字符。' );
            return self::FAILURE;
        }
        $config = config( "workerman.{$name}" );
        if ( !is_array( $config ) ) {
            $this->error( "Workerman 服务配置不存在：{$name}" );
            return self::FAILURE;
        }
        $argv = ["server_{$name}", $action];
        if ( in_array( $action, ['start', 'restart'], true ) ) { $argv[] = '-d'; }
        try {
            Server::build( $name, $config );
        }catch ( Throwable $exception ) {
            report( $exception );
            $this->error( $exception->getMessage() );
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
