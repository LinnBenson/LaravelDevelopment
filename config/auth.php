<?php

use App\Models\AdminUser;
use App\Models\User;

return [
    /**
     * 认证默认配置
     * 这个选项定义应用默认的认证 "guard" 和密码重置 "broker"。你可以按需修改，但默认值适合大多数应用。
     */
    'defaults' => [
        'guard' => env( 'AUTH_GUARD', 'web' ),
        'passwords' => env( 'AUTH_PASSWORD_BROKER', 'users' ),
    ],
    /**
     * 认证守卫
     * 接下来可以为应用定义每个认证守卫。默认配置已经使用 session 存储和 Eloquent 用户提供器。
     * 所有认证守卫都有用户提供器，用来定义如何从数据库或应用使用的其他存储系统中获取用户。通常会使用 Eloquent。
     * 支持: "session"
     */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin_users',
        ],
    ],
    /**
     * 用户提供器
     * 所有认证守卫都有用户提供器，用来定义如何从数据库或应用使用的其他存储系统中获取用户。通常会使用 Eloquent。
     * 如果有多个用户表或模型，可以配置多个提供器分别代表对应模型或表。这些提供器可以分配给你定义的额外认证守卫。
     * 支持: "database", "eloquent"
     */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env( 'AUTH_MODEL', User::class ),
        ],
        'admin_users' => [
            'driver' => 'eloquent',
            'model' => AdminUser::class,
        ],
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],
    /**
     * 密码重置
     * 这些配置指定 Laravel 密码重置功能的行为，包括用于存储令牌的表，以及实际获取用户时调用的用户提供器。
     * 过期时间表示每个重置令牌保持有效的分钟数。这个安全功能会让令牌保持较短生命周期，减少被猜中的时间。你可以按需修改。
     * 限流设置表示用户再次生成密码重置令牌前必须等待的秒数，用于防止用户快速生成大量重置令牌。
     */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env( 'AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens' ),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
    /**
     * 密码确认超时时间
     * 这里可以定义密码确认窗口过期前的秒数。过期后用户需要在确认页面重新输入密码。默认超时时间为三小时。
     */
    'password_timeout' => env( 'AUTH_PASSWORD_TIMEOUT', 10800 ),
];
