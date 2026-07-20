<?php
/**
 * Workerman 服务器配置
 * type: workerman|gateway
 */
return [
    'async' => [
        'name' => 'Async Server',
        'type' => 'workerman',
        'protocol' => 'text',
        'host' => '0.0.0.0',
        'port' => 10001,
        'user' => 'root',
        'threads' => 1,
        'event' => \App\Workerman\Events\AsyncEvent::class,
    ],
    /*
    示例配置
    'workerman' => [
        'name' => 'Workerman Server',
        'type' => 'workerman',
        'protocol' => 'text', // 协议类型
        'host' => '0.0.0.0', // 监听地址
        'port' => 10001, // 监听端口
        'user' => 'root', // 运行用户
        'threads' => 1, // 线程数
        'event' => \App\Workerman\Events\WorkermanServerEvent::class,
    ],
    'gateway' => [
        'name' => 'Gateway Server',
        'type' => 'gateway',
        'protocol' => 'websocket', // 协议类型
        'host' => '0.0.0.0', // 网关监听地址
        'port' => 10001, // 网关监听端口
        'start_port' => 10002, // 起始端口，用于 Gateway 内部通信
        'register_listen' => '0.0.0.0:1236', // 注册中心监听地址
        'register_address' => '127.0.0.1:1236', // 注册中心地址
        'user' => 'root', // 运行用户
        'threads' => 1, // Gateway 线程数
        'business_threads' => 1, // BusinessWorker 线程数
        'secret_key' => 'your_secret_key_here', // 通信密钥
        'lan_ip' => '127.0.0.1', // 内网 IP
        'ping_data' => '{"action":"ping"}', // 心跳数据
        'ping_interval' => 30, // 心跳间隔
        'ping_not_response_limit' => 1, // 心跳未响应次数限制
        'event' => \App\Workerman\Events\GatewayServerEvent::class,
    ]
    */
];