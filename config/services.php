<?php
return [
    /**
     * 第三方服务
     * 这个文件用于存储 Mailgun、Postmark、AWS 等第三方服务的凭证。它为这类信息提供约定位置，方便扩展包在统一文件中定位各种服务凭证。
     */
    'postmark' => [
        'key' => env( 'POSTMARK_API_KEY' ),
    ],
    'resend' => [
        'key' => env( 'RESEND_API_KEY' ),
    ],
    'ses' => [
        'key' => env( 'AWS_ACCESS_KEY_ID' ),
        'secret' => env( 'AWS_SECRET_ACCESS_KEY' ),
        'region' => env( 'AWS_DEFAULT_REGION', 'us-east-1' ),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env( 'SLACK_BOT_USER_OAUTH_TOKEN' ),
            'channel' => env( 'SLACK_BOT_USER_DEFAULT_CHANNEL' ),
        ],
    ],
];
