<?php

use Illuminate\Support\Str;

return [
    /**
     * 默认 Session 驱动
     * 这个选项决定处理传入请求时使用的默认 Session 驱动。Laravel 支持多种存储选项来持久化 Session 数据，数据库存储是一个很好的默认选择。
     * 支持: "file", "cookie", "database", "memcached", "redis", "dynamodb", "array"
     */
    'driver' => env( 'SESSION_DRIVER', 'database' ),
    /**
     * Session 生命周期
     * 这里可以指定 Session 允许空闲多少分钟后过期。如果希望浏览器关闭后立即过期，可以通过 expire_on_close 配置项指定。
     */
    'lifetime' => (int) env( 'SESSION_LIFETIME', 120 ),
    'expire_on_close' => env( 'SESSION_EXPIRE_ON_CLOSE', false ),
    /**
     * Session 加密
     * 这个选项可以方便地指定所有 Session 数据在存储前都要加密。所有加密都由 Laravel 自动完成，你可以像平常一样使用 Session。
     */
    'encrypt' => env( 'SESSION_ENCRYPT', false ),
    /**
     * Session 文件位置
     * 使用 "file" Session 驱动时，Session 文件会放在磁盘上。这里定义默认存储位置，你也可以自由提供其他存储位置。
     */
    'files' => storage_path( 'framework/sessions' ),
    /**
     * Session 数据库连接
     * 使用 "database" 或 "redis" Session 驱动时，可以指定用于管理这些 Session 的连接。它应对应数据库配置选项中的一个连接。
     */
    'connection' => env( 'SESSION_CONNECTION' ),
    /**
     * Session 数据表
     * 使用 "database" Session 驱动时，可以指定用于存储 Session 的数据表。默认值已经合理定义，你也可以改为其他表。
     */
    'table' => env( 'SESSION_TABLE', 'sessions' ),
    /**
     * Session 缓存存储
     * 使用框架中基于缓存驱动的 Session 后端时，可以定义用于在请求之间存储 Session 数据的缓存存储。它必须匹配已定义的某个缓存存储。
     * 影响: "dynamodb", "memcached", "redis"
     */
    'store' => env( 'SESSION_STORE' ),
    /**
     * Session 清理概率
     * 某些 Session 驱动必须手动清理存储位置中的旧 Session。这里定义在某次请求中触发清理的概率，默认概率是 100 次中 2 次。
     */
    'lottery' => [2, 100],
    /**
     * Session Cookie 名称
     * 这里可以修改框架创建的 Session Cookie 名称。通常不需要修改这个值，因为修改它不会带来明显安全提升。
     */
    'cookie' => env( 'SESSION_COOKIE',
        Str::slug( (string) env( 'APP_NAME', 'laravel' ) ) . '-session' ),
    /**
     * Session Cookie 路径
     * Session Cookie 路径决定 Cookie 被视为可用的路径。通常这是应用根路径，但必要时可以自由修改。
     */
    'path' => env( 'SESSION_PATH', '/' ),
    /**
     * Session Cookie 域名
     * 这个值决定 Session Cookie 可用的域名和子域名。默认情况下，Cookie 可用于不包含子域名的根域名。通常不应修改。
     */
    'domain' => env( 'SESSION_DOMAIN' ),
    /**
     * 仅 HTTPS Cookie
     * 将这个选项设置为 true 后，只有浏览器使用 HTTPS 连接时才会把 Session Cookie 发回服务器，避免在不安全连接中发送 Cookie。
     */
    'secure' => env( 'SESSION_SECURE_COOKIE' ),
    /**
     * 仅 HTTP 访问
     * 将这个值设置为 true 会阻止 JavaScript 访问 Cookie 值，Cookie 只能通过 HTTP 协议访问。通常不应禁用这个选项。
     */
    'http_only' => env( 'SESSION_HTTP_ONLY', true ),
    /**
     * Same-Site Cookie
     * 这个选项决定发生跨站请求时 Cookie 的行为，可用于缓解 CSRF 攻击。默认设置为 "lax"，以允许安全的跨站请求。
     * 参考: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
     * 支持: "lax", "strict", "none", null
     */
    'same_site' => env( 'SESSION_SAME_SITE', 'lax' ),
    /**
     * 分区 Cookie
     * 将这个值设置为 true 会在跨站上下文中把 Cookie 绑定到顶级站点。当标记为 "secure" 且 Same-Site 属性设为 "none" 时，浏览器会接受分区 Cookie。
     */
    'partitioned' => env( 'SESSION_PARTITIONED_COOKIE', false ),
];
