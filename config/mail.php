<?php
return [
    /**
     * 默认邮件发送器
     * 这个选项控制发送所有邮件时默认使用的邮件发送器，除非发送时明确指定其他发送器。其他发送器都可以在 "mailers" 数组中配置，并提供了各类型示例。
     */
    'default' => env( 'MAIL_MAILER', 'log' ),
    /**
     * 邮件发送器配置
     * 这里可以配置应用使用的所有邮件发送器及其对应设置。已经提供了几个示例，你可以根据应用需要自由添加。
     * Laravel 支持多种发送邮件时可用的邮件 "transport" 驱动。你可以在下面指定邮件发送器使用哪一种，也可以按需添加更多发送器。
     * 支持: "smtp", "sendmail", "mailgun", "ses", "ses-v2", "postmark", "resend", "log", "array", "failover", "roundrobin"
     */
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env( 'MAIL_SCHEME' ),
            'url' => env( 'MAIL_URL' ),
            'host' => env( 'MAIL_HOST', '127.0.0.1' ),
            'port' => env( 'MAIL_PORT', 2525 ),
            'username' => env( 'MAIL_USERNAME' ),
            'password' => env( 'MAIL_PASSWORD' ),
            'timeout' => null,
            'local_domain' => env( 'MAIL_EHLO_DOMAIN', parse_url( (string) env( 'APP_URL', 'http://localhost' ), PHP_URL_HOST ) ),
        ],
        'ses' => [
            'transport' => 'ses',
        ],
        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env( 'POSTMARK_MESSAGE_STREAM_ID' ),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],
        'resend' => [
            'transport' => 'resend',
        ],
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env( 'MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i' ),
        ],
        'log' => [
            'transport' => 'log',
            'channel' => env( 'MAIL_LOG_CHANNEL' ),
        ],
        'array' => [
            'transport' => 'array',
        ],
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],
    ],
    /**
     * 全局发件人地址
     * 你可能希望应用发送的所有邮件都使用同一个发件地址。这里可以指定应用发送所有邮件时全局使用的名称和地址。
     */
    'from' => [
        'address' => env( 'MAIL_FROM_ADDRESS', 'hello@example.com' ),
        'name' => env( 'MAIL_FROM_NAME', env( 'APP_NAME', 'Laravel' ) ),
    ],
];
