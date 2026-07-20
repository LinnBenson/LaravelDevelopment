<?php

namespace App\Workerman;

use Workerman\Worker;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Timer;
use Illuminate\Support\Facades\Cache;

class Server {

    /**
     * 服务名称
     * @var string
     */
    public static string $name;

    /**
     * 服务配置
     * @var array
     */
    public static array $config;

    /**
     * 事件处理类
     * @var object
     */
    public static ?object $event = null;

    /**
     * Workerman 对象
     * @var Worker
     */
    public static ?Worker $worker = null;

    /**
     * Register 对象
     * @var Register
     */
    public static ?Register $register = null;

    /**
     * Gateway 对象
     * @var Gateway
     */
    public static ?Gateway $gateway = null;

    /**
     * BusinessWorker 对象
     * @var BusinessWorker
     */
    public static ?BusinessWorker $business = null;

    /**
     * 定时器列表
     * @var array
     */
    public static array $timers = [];

    /**
     * 注册应用服务
     * @param string $name 服务名称。
     * @param array $config 配置数组。
     * @return void
     */
    public static function build( string $name, array $config ): void {
        // 配置完整性检查
        $mustKeys = [ 'name', 'type', 'protocol', 'host', 'port', 'user', 'threads', 'event' ];
        foreach ( $mustKeys as $key ) {
            if ( !array_key_exists( $key, $config ) ) {
                throw new \InvalidArgumentException( "[{$name}] Workerman required configuration key: {$key}" );
            }
        }
        // 事件处理类检查
        if ( !class_exists( $config['event'] ) ) {
            throw new \InvalidArgumentException( "[{$name}] Workerman event class not found: {$config['event']}" );
        }
        // 注册服务
        self::$name = $name;
        self::$config = $config;
        switch ( self::$config['type'] ) {
            case 'workerman':
                self::buildWorkerman();
                break;
            case 'gateway':
                self::buildGateway();
                break;

            default:
                throw new \InvalidArgumentException( "[{$name}] Unknown build type: ".self::$config['type'] );
        }
    }

    /**
     * 获取服务状态
     * @param string $name 服务名称。
     * @return bool 服务是否运行。
     */
    public static function status( string $name ): bool {
        $status = Cache::get( "ServerStatus:{$name}", null );
        if ( $status === null || empty( $status ) || !is_numeric( $status ) || ( time() - $status > 16 ) ) {
            return false;
        }
        return true;
    }

    /**
     * 设置定时器
     * @param string $name 定时器名称。
     * @param mixed ...$args 定时器参数。
     * @return bool|int 成功返回定时器 ID，失败返回 false。
     */
    public static function setTimer( string $name, ...$args ): bool|int {
        // 检查定时器是否已存在
        if ( array_key_exists( $name, self::$timers ) ) { return false; }
        // 创建定时器
        $timerId = Timer::add( ...$args );
        self::$timers[$name] = $timerId;
        return $timerId;
    }

    /**
     * 删除定时器
     * @param string $name 定时器名称。
     * @return bool 是否成功删除。
     */
    public static function delTimer( string $name ): bool {
        if ( !array_key_exists( $name, self::$timers ) ) { return false; }
        Timer::del( self::$timers[$name] );
        unset( self::$timers[$name] );
        return true;
    }

    /**
     * 构建 Workerman 服务器
     * @return void
     */
    private static function buildWorkerman(): void {
        // 日志输出位置
        $log = storage_path( "logs/workerman" );
        if ( !is_dir( $log ) ) { mkdir( $log, 0755, true ); }
        if ( !file_exists( "{$log}/".self::$name.".log" ) ) { touch( "{$log}/".self::$name.".log" ); }
        Worker::$logFile = "{$log}/".self::$name.".log";
        Worker::$stdoutFile = "{$log}/".self::$name.".log";
        // 注册 PID 文件位置
        Worker::$pidFile = "{$log}/".self::$name.".pid";
        // 创建 Worker 对象
        self::$worker = new Worker( self::$config['protocol']."://".self::$config['host'].":".self::$config['port'] );
        self::$event = new self::$config['event']( self::$worker );
        // 设置服务器名称
        self::$worker->name = self::$config['name'];
        // 设置运行用户
        self::$worker->user = self::$config['user'];
        // 设置进程数
        self::$worker->count = self::$config['threads'];
        // 注册事件回调
        $actions = [
            'onConnect', 'onWebSocketConnect', 'onWebSocketConnected',
            'onMessage',
            'onBufferFull', 'onBufferDrain',
            'onWorkerStart', 'onClose', 'onError',
            'onWorkerStop', 'onWorkerReload',
            'onWebSocketClose', 'onWebSocketPing', 'onWebSocketPong'
        ];
        $default = [
            'onWorkerStart' => function( $worker ) {
                self::setServerStatusTimer();
            }
        ];
        foreach ( $actions as $event ) {
            if ( is_public( self::$event, $event ) && array_key_exists( $event, $default ) ) {
                self::$worker->$event = function( ...$args )use ( $default, $event ) {
                    $default[$event]( ...$args );
                    self::$event->$event( ...$args );
                };
            }else if ( is_public( self::$event, $event ) ) {
                self::$worker->$event = function( ...$args )use ( $event ) {
                    self::$event->$event( ...$args );
                };
            }else if ( array_key_exists( $event, $default ) ) {
                self::$worker->$event = $default[$event];
            }
        }
        // 运行服务
        Worker::runAll();
    }

    private static function buildGateway(): void {
        // 日志及进程文件
        $log = storage_path( "logs/workerman" );
        if ( !is_dir( $log ) ) { mkdir( $log, 0755, true ); }
        if ( !file_exists( "{$log}/".self::$name.".log" ) ) { touch( "{$log}/".self::$name.".log" ); }
        Worker::$logFile = "{$log}/".self::$name.".log";
        Worker::$stdoutFile = "{$log}/".self::$name.".log";
        Worker::$pidFile = "{$log}/".self::$name.".pid";
        // 注册中心地址及通信密钥
        $registerListen = self::$config['register_listen'] ?? '0.0.0.0:1236';
        $registerAddress = self::$config['register_address'] ?? '127.0.0.1:1236';
        $secretKey = self::$config['secret_key'] ?? bin2hex( random_bytes( 32 ) );
        // 注册中心负责协调 Gateway 与 BusinessWorker。
        self::$register = new Register( "text://{$registerListen}" );
        self::$register->name = self::$config['name'].' Register';
        self::$register->user = self::$config['user'];
        self::$register->secretKey = $secretKey;
        // Gateway 负责维护客户端连接并转发消息。
        self::$gateway = new Gateway( self::$config['protocol']."://".self::$config['host'].":".self::$config['port'] );
        self::$gateway->name = self::$config['name'].' Gateway';
        self::$gateway->user = self::$config['user'];
        self::$gateway->count = self::$config['threads'];
        self::$gateway->lanIp = self::$config['lan_ip'] ?? '127.0.0.1';
        self::$gateway->startPort = self::$config['start_port'] ?? ( self::$config['port'] + 1 );
        self::$gateway->registerAddress = $registerAddress;
        self::$gateway->secretKey = $secretKey;
        self::$gateway->pingInterval = self::$config['ping_interval'] ?? 30;
        self::$gateway->pingNotResponseLimit = self::$config['ping_not_response_limit'] ?? 1;
        self::$gateway->pingData = self::$config['ping_data'] ?? '{"action":"ping"}';
        // BusinessWorker 负责执行应用事件处理逻辑。
        self::$business = new BusinessWorker();
        self::$business->name = self::$config['name'].' BusinessWorker';
        self::$business->user = self::$config['user'];
        self::$business->count = self::$config['business_threads'] ?? self::$config['threads'];
        self::$business->registerAddress = $registerAddress;
        self::$business->secretKey = $secretKey;
        self::$business->eventHandler = self::$config['event'];
        self::$business->onWorkerStart = function() { self::setServerStatusTimer(); };
        self::$event = new self::$config['event']( self::$worker );
        // 运行服务
        Worker::runAll();
    }

    /**
     * 设置服务器状态定时器
     * @return int 定时器 ID。
     */
    private static function setServerStatusTimer(): int {
        return self::setTimer( 'ServerStatus', 5, function() {
            Cache::put( "ServerStatus:".self::$name, time(), 8 );
        });
    }
}
