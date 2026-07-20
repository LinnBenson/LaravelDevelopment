<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    /**
     * 默认日志频道
     * 这个选项定义用于写入日志消息的默认日志频道。这里提供的值应匹配下面 "channels" 配置列表中的某个频道。
     */
    'default' => env( 'LOG_CHANNEL', 'stack' ),
    /**
     * 弃用警告日志频道
     * 这个选项控制记录 PHP 和库弃用功能警告时使用的日志频道，帮助应用为依赖的未来大版本做好准备。
     */
    'deprecations' => [
        'channel' => env( 'LOG_DEPRECATIONS_CHANNEL', 'null' ),
        'trace' => env( 'LOG_DEPRECATIONS_TRACE', false ),
    ],
    /**
     * 日志频道
     * 这里可以配置应用的日志频道。Laravel 使用 Monolog PHP 日志库，其中包含多种强大的日志处理器和格式化器，可按需使用。
     * 可用驱动: "single", "daily", "slack", "syslog", "errorlog", "monolog", "custom", "stack"
     */
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => explode( ',', (string) env( 'LOG_STACK', 'single' ) ),
            'ignore_exceptions' => false,
        ],
        'single' => [
            'driver' => 'single',
            'path' => storage_path( 'logs/laravel.log' ),
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'replace_placeholders' => true,
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path( 'logs/laravel.log' ),
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'days' => env( 'LOG_DAILY_DAYS', 14 ),
            'replace_placeholders' => true,
        ],
        'slack' => [
            'driver' => 'slack',
            'url' => env( 'LOG_SLACK_WEBHOOK_URL' ),
            'username' => env( 'LOG_SLACK_USERNAME', env( 'APP_NAME', 'Laravel' ) ),
            'emoji' => env( 'LOG_SLACK_EMOJI', ':boom:' ),
            'level' => env( 'LOG_LEVEL', 'critical' ),
            'replace_placeholders' => true,
        ],
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'handler' => env( 'LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class ),
            'handler_with' => [
                'host' => env( 'PAPERTRAIL_URL' ),
                'port' => env( 'PAPERTRAIL_PORT' ),
                'connectionString' => 'tls://' . env( 'PAPERTRAIL_URL' ) . ':' . env( 'PAPERTRAIL_PORT' ),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],
        'stderr' => [
            'driver' => 'monolog',
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env( 'LOG_STDERR_FORMATTER' ),
            'processors' => [PsrLogMessageProcessor::class],
        ],
        'syslog' => [
            'driver' => 'syslog',
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'facility' => env( 'LOG_SYSLOG_FACILITY', LOG_USER ),
            'replace_placeholders' => true,
        ],
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env( 'LOG_LEVEL', 'debug' ),
            'replace_placeholders' => true,
        ],
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],
        'emergency' => [
            'path' => storage_path( 'logs/laravel.log' ),
        ],
    ],
];
