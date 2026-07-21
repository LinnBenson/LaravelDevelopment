<?php

use Illuminate\Support\Str;

return [
    /**
     * 默认缓存存储
     * 这个选项控制框架默认使用的缓存存储。当应用中执行缓存操作且未明确指定其他连接时，会使用这个连接。
     */
    'default' => env( 'CACHE_STORE', 'database' ),
    /**
     * 缓存存储
     * 这里可以定义应用的所有缓存 "stores" 及其驱动。你也可以为同一个缓存驱动定义多个存储，用于分组不同类型的缓存项。
     * 支持的驱动: "array", "database", "file", "memcached", "redis", "dynamodb", "octane", "failover", "null"
     */
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'database' => [
            'driver' => 'database',
            'connection' => env( 'DB_CACHE_CONNECTION' ),
            'table' => env( 'DB_CACHE_TABLE', 'cache' ),
            'lock_connection' => env( 'DB_CACHE_LOCK_CONNECTION' ),
            'lock_table' => env( 'DB_CACHE_LOCK_TABLE' ),
        ],
        'file' => [
            'driver' => 'file',
            'path' => storage_path( 'framework/cache/data' ),
            'lock_path' => storage_path( 'framework/cache/data' ),
        ],
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env( 'MEMCACHED_PERSISTENT_ID' ),
            'sasl' => [
                env( 'MEMCACHED_USERNAME' ),
                env( 'MEMCACHED_PASSWORD' ),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env( 'MEMCACHED_HOST', '127.0.0.1' ),
                    'port' => env( 'MEMCACHED_PORT', 11211 ),
                    'weight' => 100,
                ],
            ],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => env( 'REDIS_CACHE_CONNECTION', 'cache' ),
            'lock_connection' => env( 'REDIS_CACHE_LOCK_CONNECTION', 'default' ),
        ],
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env( 'AWS_ACCESS_KEY_ID' ),
            'secret' => env( 'AWS_SECRET_ACCESS_KEY' ),
            'region' => env( 'AWS_DEFAULT_REGION', 'us-east-1' ),
            'table' => env( 'DYNAMODB_CACHE_TABLE', 'cache' ),
            'endpoint' => env( 'DYNAMODB_ENDPOINT' ),
        ],
        'octane' => [
            'driver' => 'octane',
        ],
        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'database',
                'array',
            ],
        ],
    ],
    /**
     * 缓存键前缀
     * 使用 APC、database、memcached、Redis 和 DynamoDB 缓存存储时，可能有其他应用共用同一缓存。因此可以给每个缓存键添加前缀以避免冲突。
     */
    'prefix' => env( 'CACHE_PREFIX', Str::slug( (string) env( 'APP_NAME', 'laravel' ) ) . '-cache-' ),
    /**
     * 调试日志文件
     */
    'debug' => storage_path( "logs/debug_echo.log" )
];
