<?php

return [
    /**
     * 系统配置上传
     * 修改扩展名列表后执行 php artisan config:clear 即可生效。
     */
    'system_config' => [
        'disk' => 'public',
        'directory' => 'system_file',
        'image_max_size' => 2048,
        'file_max_size' => 10240,
        'image_extensions' => ['png', 'jpeg', 'jpg', 'gif', 'webp'],
        'image_mime_types' => ['image/png', 'image/jpeg', 'image/gif', 'image/webp'],
        'file_extensions' => ['txt', 'md', 'pdf', 'png', 'jpeg', 'jpg', 'gif', 'svg'],
        'file_mime_types' => [
            'text/plain',
            'text/markdown',
            'application/pdf',
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/svg+xml',
        ],
    ],
];
