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
        {action : start、restart、stop、status、debug}
        {--d|daemon : 以守护进程方式启动或重启服务}';

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
        if ( !in_array( $action, ['start', 'restart', 'stop', 'status', 'debug' ], true ) ) {
            $this->error( "[{$action}] Allow actions: start, restart, stop, status, debug." );
            return self::FAILURE;
        }
        if ( preg_match( '/^[A-Za-z0-9_]+$/', $name ) !== 1 ) {
            $this->error( "[{$name}] Incorrect service identifier." );
            return self::FAILURE;
        }
        $config = config( "workerman.{$name}" );
        if ( !is_array( $config ) ) {
            $this->error( "[{$name}] Workerman service configuration does not exist." );
            return self::FAILURE;
        }
        // 调试命令
        if ( $action === 'debug' ) {
            $max = 9999;
            $cacheFile = config( 'cache.debug' );
            for(  $i = 0; $i < $max; $i++ ) {
                shell_exec( "php artisan server {$name} restart -d > {$cacheFile} 2>&1" );
                $cache = file_exists( $cacheFile ) ? file_get_contents( $cacheFile ) : '';
                $this->line( $cache );
                $this->line( "<fg=green>Use 'stop' to stop this debugging task; other operations will refresh it.</>" );
                $this->output->write( "<fg=green>[".( $i + 1 )."/{$max}] ></> " );
                $input = trim( (string) fgets( STDIN ) );
                if ( $input === 'stop' ) {
                    shell_exec( "php artisan server {$name} stop > {$cacheFile} 2>&1" );
                    if ( file_exists( $cacheFile ) ) {
                        $this->line( file_get_contents( $cacheFile ) );
                        unlink( $cacheFile );
                    }
                    return self::SUCCESS;
                }
            }
            return self::SUCCESS;
        }
        // 其它命令
        $argv = ["server_{$name}", $action];
        if ( $this->option( 'daemon' ) && in_array( $action, ['start', 'restart'], true ) ) { $argv[] = '-d'; }
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
