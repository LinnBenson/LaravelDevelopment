<?php

namespace App\Filament\Resources\SystemSettings\ServiceManagement;

use App\Workerman\Server;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use RuntimeException;
use Throwable;
use UnitEnum;

/**
 * ServiceManagement
 * Workerman 服务项管理页面。
 * @package App\Filament\Resources\SystemSettings\ServiceManagement
 */
class ServiceManagement extends Page {
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|UnitEnum|null $navigationGroup = '系统设置';

    protected static ?string $navigationLabel = '服务项管理';

    protected static ?string $title = '服务项管理';

    protected static ?string $slug = 'system-settings/services';

    protected static ?int $navigationSort = 2;

    protected string $view = 'Filament.SystemSettings.ServiceManagement.service-management';

    /** @var array<string, bool> */
    public array $expandedLogs = [];

    /** @var array<string, string> */
    public array $logContents = [];

    public ?string $statusServiceName = null;

    public string $statusOutput = '';

    /**
     * 获取服务项列表。
     * 根据 Workerman 配置、状态心跳和 PID 文件生成页面数据。
     * @return array<int, array<string, bool|int|string|null>> 服务项列表
     */
    public function getServices(): array {
        $configs = config( 'workerman', [] );
        if ( !is_array( $configs ) ) { return []; }
        $services = [];
        foreach ( $configs as $key => $config ) {
            if ( !is_string( $key ) || !preg_match( '/^[A-Za-z0-9_-]+$/', $key ) || !is_array( $config ) ) { continue; }
            $pid = $this->getPid( $key );
            $services[] = [
                'key' => $key,
                'name' => (string) ( $config['name'] ?? $key ),
                'type' => (string) ( $config['type'] ?? 'unknown' ),
                'protocol' => (string) ( $config['protocol'] ?? 'unknown' ),
                'host' => (string) ( $config['host'] ?? '-' ),
                'port' => isset( $config['port'] ) ? (int) $config['port'] : null,
                'threads' => isset( $config['threads'] ) ? (int) $config['threads'] : 0,
                'running' => Server::status( $key ) || !empty( $pid ),
                'pid' => $pid,
            ];
        }
        return $services;
    }

    /**
     * 查看服务日志。
     * 读取指定服务日志的最后 100 行。
     * @param string $name 服务标识
     * @return void
     */
    public function viewLog( string $name ): void {
        if ( !$this->hasService( $name ) ) {
            $this->notifyFailure( '服务配置不存在。' );
            return;
        }
        $this->expandedLogs[$name] = true;
        try {
            $this->logContents[$name] = $this->readLastLines( $this->getLogPath( $name ) );
        }catch ( Throwable ) {
            $this->logContents[$name] = '日志读取失败，请检查文件权限。';
            $this->notifyFailure( $this->logContents[$name] );
        }
    }

    /**
     * 展开或收起服务日志。
     * 每个服务独立维护日志展开状态。
     * @param string $name 服务标识
     * @return void
     */
    public function toggleLog( string $name ): void {
        if ( $this->expandedLogs[$name] ?? false ) {
            unset( $this->expandedLogs[$name], $this->logContents[$name] );
            return;
        }
        $this->viewLog( $name );
    }

    /**
     * 刷新服务状态。
     * 校验服务项后通过 Livewire 重新渲染当前服务数据。
     * @param string $name 服务标识
     * @return void
     */
    public function refreshService( string $name ): void {
        if ( !$this->hasService( $name ) ) { $this->notifyFailure( '服务配置不存在。' ); }
    }

    /**
     * 显示服务命令行状态。
     * 执行一次 Workerman status 命令并打开结果弹窗。
     * @param string $name 服务标识
     * @return void
     */
    public function showStatus( string $name ): void {
        if ( !$this->hasService( $name ) ) { $this->notifyFailure( '服务配置不存在。' ); return; }
        $this->statusServiceName = $name;
        $this->statusOutput = $this->runServerStatusCommand( $name );
        $this->mountAction( 'statusService' );
    }

    /**
     * 获取服务状态弹窗操作。
     * @return Action 状态弹窗操作
     */
    public function statusServiceAction(): Action {
        return Action::make( 'statusService' )
            ->modalHeading( fn (): string => "{$this->statusServiceName} 服务状态" )
            ->modalDescription( 'Workerman status 命令的即时输出。' )
            ->modalContent( fn () => view( 'Filament.SystemSettings.ServiceManagement.service-status', [
                'output' => $this->statusOutput,
            ] ) )
            ->modalSubmitAction( false )
            ->modalCancelActionLabel( '关闭' );
    }

    /**
     * 启动服务。
     * 预留服务启动操作入口。
     * @param string $name 服务标识
     * @return void
     */
    public function startService( string $name ): void {
        if ( !$this->hasService( $name ) ) { $this->notifyFailure( '服务配置不存在。' ); return; }
        $this->runServerCommand( $name, 'start' );
        Notification::make()->title( "已尝试启动 {$name} 服务" )->info()->send();
    }

    /**
     * 重启服务。
     * 预留服务重启操作入口。
     * @param string $name 服务标识
     * @return void
     */
    public function restartService( string $name ): void {
        if ( !$this->hasService( $name ) ) { $this->notifyFailure( '服务配置不存在。' ); return; }
        $this->runServerCommand( $name, 'restart' );
        Notification::make()->title( "已尝试重启 {$name} 服务" )->info()->send();
    }

    /**
     * 停止服务。
     * 预留服务停止操作入口。
     * @param string $name 服务标识
     * @return void
     */
    public function stopService( string $name ): void {
        if ( !$this->hasService( $name ) ) { $this->notifyFailure( '服务配置不存在。' ); return; }
        $this->runServerCommand( $name, 'stop' );
        Notification::make()->title( "已尝试停止 {$name} 服务" )->info()->send();
    }

    /**
     * 获取页面面包屑。
     * @return array<string> 面包屑列表
     */
    public function getBreadcrumbs(): array {
        return ['系统设置', '服务项管理'];
    }

    /**
     * 检查服务配置是否存在。
     * @param string $name 服务标识
     * @return bool 是否存在
     */
    private function hasService( string $name ): bool {
        return preg_match( '/^[A-Za-z0-9_-]+$/', $name ) === 1 && is_array( config( "workerman.{$name}" ) );
    }

    /**
     * 执行服务管理命令。
     * 断开命令输出管道，避免守护进程阻塞 Livewire 请求。
     * @param string $name 服务标识
     * @param string $action 服务操作
     * @return void
     */
    private function runServerCommand( string $name, string $action ): void {
        $php = escapeshellarg( PHP_BINDIR.'/php' );
        $artisan = escapeshellarg( base_path( 'artisan' ) );
        shell_exec( "{$php} {$artisan} server {$name} {$action} -d > /dev/null 2>&1" );
    }

    /**
     * 获取服务命令行状态。
     * 执行单次 status 命令并清理终端控制字符。
     * @param string $name 服务标识
     * @return string 命令行状态
     */
    private function runServerStatusCommand( string $name ): string {
        $php = escapeshellarg( PHP_BINDIR.'/php' );
        $artisan = escapeshellarg( base_path( 'artisan' ) );
        $output = shell_exec( "{$php} {$artisan} server {$name} status 2>&1" );
        if ( $output === null || trim( $output ) === '' ) { return '命令未返回状态信息。'; }
        $output = preg_replace( '/\e\[[0-?]*[ -\/]*[@-~]/', '', $output );
        return trim( $output ?? '命令状态解析失败。' );
    }

    /**
     * 获取服务 PID。
     * @param string $name 服务标识
     * @return int|null PID
     */
    private function getPid( string $name ): ?int {
        $path = $this->getPidPath( $name );
        if ( !is_file( $path ) || !is_readable( $path ) ) { return null; }
        $pid = trim( (string) file_get_contents( $path ) );
        return ctype_digit( $pid ) && (int) $pid > 0 ? (int) $pid : null;
    }

    /**
     * 获取固定日志路径。
     * @param string $name 服务标识
     * @return string 日志路径
     */
    private function getLogPath( string $name ): string {
        return storage_path( "logs/workerman/{$name}.log" );
    }

    /**
     * 获取固定 PID 路径。
     * @param string $name 服务标识
     * @return string PID 路径
     */
    private function getPidPath( string $name ): string {
        return storage_path( "logs/workerman/{$name}.pid" );
    }

    /**
     * 读取文件最后 100 行。
     * @param string $path 日志路径
     * @return string 日志内容
     */
    private function readLastLines( string $path ): string {
        if ( !is_file( $path ) ) { return '日志文件尚未生成。'; }
        if ( !is_readable( $path ) ) { throw new RuntimeException( '日志文件不可读。' ); }
        $maxLines = 100;
        $maxBytes = 2 * 1024 * 1024;
        $blockSize = 8192;
        $fileSize = filesize( $path );
        if ( $fileSize === false ) { throw new RuntimeException( '无法获取日志大小。' ); }
        $handle = fopen( $path, 'rb' );
        if ( $handle === false ) { throw new RuntimeException( '日志文件无法打开。' ); }
        $position = $fileSize;
        $content = '';
        try {
            while ( $position > 0 && substr_count( $content, "\n" ) <= $maxLines && strlen( $content ) < $maxBytes ) {
                $readBytes = min( $blockSize, $position );
                $position -= $readBytes;
                if ( fseek( $handle, $position ) !== 0 ) { throw new RuntimeException( '日志文件定位失败。' ); }
                $block = fread( $handle, $readBytes );
                if ( $block === false ) { throw new RuntimeException( '日志读取失败。' ); }
                $content = "{$block}{$content}";
            }
        }finally {
            fclose( $handle );
        }
        if ( !mb_check_encoding( $content, 'UTF-8' ) ) { $content = mb_scrub( $content, 'UTF-8' ); }
        $content = rtrim( $content, "\r\n" );
        if ( $content === '' ) { return '日志文件为空。'; }
        $lines = preg_split( '/\R/', $content );
        if ( $lines === false ) { throw new RuntimeException( '日志解析失败。' ); }
        return implode( "\n", array_slice( $lines, -$maxLines ) );
    }

    /**
     * 显示操作失败通知。
     * @param string $message 提示内容
     * @return void
     */
    private function notifyFailure( string $message ): void {
        Notification::make()->title( $message )->danger()->send();
    }
}
