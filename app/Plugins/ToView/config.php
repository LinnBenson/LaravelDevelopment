<?php
    return [
        'upload' => [
            // 上传文件存放路径
            'path' => 'private/uploads',
            // 上传文件大小限制，单位字节，默认 50MB
            'size' => 1024 * 1024 * 50,
            // 上传文件类型限制，默认允许常见图片、视频、音频、压缩包、文档等格式
            'ext' => [
                'jpg' => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png' => ['image/png'],
                'gif' => ['image/gif'],
                'bmp' => ['image/bmp', 'image/x-ms-bmp'],
                'webp' => ['image/webp'],
                'svg' => ['image/svg+xml', 'text/xml'],
                'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
                'mp4' => ['video/mp4'],
                'avi' => ['video/x-msvideo'],
                'mov' => ['video/quicktime'],
                'wmv' => ['video/x-ms-wmv'],
                'flv' => ['video/x-flv'],
                'mp3' => ['audio/mpeg'],
                'wav' => ['audio/wav', 'audio/x-wav'],
                'flac' => ['audio/flac', 'audio/x-flac'],
                'aac' => ['audio/aac', 'audio/x-aac', 'audio/mp4'],
                'zip' => ['application/zip', 'application/x-zip-compressed'],
                'rar' => ['application/vnd.rar', 'application/x-rar-compressed'],
                '7z' => ['application/x-7z-compressed'],
                'tar' => ['application/x-tar'],
                'gz' => ['application/gzip', 'application/x-gzip'],
                'bz2' => ['application/x-bzip2'],
                'xz' => ['application/x-xz'],
                'pdf' => ['application/pdf'],
                'doc' => ['application/msword'],
                'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'xls' => ['application/vnd.ms-excel'],
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'ppt' => ['application/vnd.ms-powerpoint'],
                'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
                'txt' => ['text/plain'],
                'md' => ['text/markdown', 'text/plain'],
                'csv' => ['text/csv', 'text/plain'],
                'json' => ['application/json', 'text/plain'],
                'xml' => ['application/xml', 'text/xml'],
                'yml' => ['application/yaml', 'text/yaml', 'text/plain'],
                'yaml' => ['application/yaml', 'text/yaml', 'text/plain'],
                'html' => ['text/html', 'text/plain'],
                'htm' => ['text/html', 'text/plain'],
                'css' => ['text/css', 'text/plain'],
                'js' => ['text/javascript', 'application/javascript', 'text/plain'],
                'ts' => ['text/typescript', 'application/typescript', 'text/plain'],
                'jsx' => ['text/jsx', 'text/plain'],
                'tsx' => ['text/tsx', 'text/plain'],
                'vue' => ['text/vue', 'text/plain']
            ]
        ]
    ];