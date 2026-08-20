<?php

namespace App\Plugins\ToView\Support;

use Illuminate\Http\Request;

/**
 * PostService
 * 表单提交相关业务服务。
 * @package App\Plugins\ToView\Support
 */
class PostService {
    /**
     * 移动临时文件
     * 将 ToView 上传组件生成的临时文件移动到 storage/app/public 下的目标目录，并返回公开访问链接。
     * @param string $fileLink 临时文件链接
     * @param string $target 相对于 storage/app/public 的目标目录
     * @return string|false 移动成功时返回访问链接，失败时返回 false
     */
    public static function moveTmp( string $fileLink, string $target ): bool|string {
        $uploadConfig = plugin( 'ToView' )->config( 'upload' );
        $linkPath = parse_url( trim( $fileLink ), PHP_URL_PATH );
        if ( !is_string( $linkPath ) || $linkPath === '' ) { return false; }
        $fileName = rawurldecode( basename( $linkPath ) );
        $temporaryRoute = route( 'plugins.to-view.upload.tmp', ['file' => $fileName], false );
        if ( $linkPath !== $temporaryRoute ) { return false; }
        $extension = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
        if ( !preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.[a-z0-9]+$/i', $fileName ) ) { return false; }
        if ( !array_key_exists( $extension, $uploadConfig['ext'] ) ) { return false; }
        $target = self::normalizeTarget( $target );
        if ( $target === null ) { return false; }
        $publicRoot = storage_path( 'app/public' );
        if ( !is_dir( $publicRoot ) && !mkdir( $publicRoot, 0750, true ) && !is_dir( $publicRoot ) ) { return false; }
        $storageRoot = realpath( $publicRoot );
        $temporaryDirectory = realpath( storage_path( 'app/'.trim( $uploadConfig['path'], '/' ) ) );
        if ( $storageRoot === false || $temporaryDirectory === false ) { return false; }
        $source = realpath( "{$temporaryDirectory}/{$fileName}" );
        if ( $source === false || !is_file( $source ) || !self::pathInside( $source, $temporaryDirectory ) ) { return false; }
        $targetDirectory = "{$publicRoot}/{$target}";
        if ( !is_dir( $targetDirectory ) && !mkdir( $targetDirectory, 0750, true ) && !is_dir( $targetDirectory ) ) { return false; }
        $targetDirectory = realpath( $targetDirectory );
        if ( $targetDirectory === false || !self::pathInside( $targetDirectory, $storageRoot ) || $targetDirectory === $temporaryDirectory ) { return false; }
        $destination = "{$targetDirectory}/{$fileName}";
        if ( file_exists( $destination ) ) { return false; }
        if ( !link( $source, $destination ) ) { return false; }
        if ( unlink( $source ) ) {
            $publicTarget = implode( '/', array_map( 'rawurlencode', explode( '/', $target ) ) );
            return "/storage/{$publicTarget}/{$fileName}";
        }
        unlink( $destination );
        return false;
    }

    /**
     * 整理目标目录
     * @param string $target 目标目录
     * @return string|null 标准化目录，非法时返回 null
     */
    private static function normalizeTarget( string $target ): ?string {
        $target = trim( str_replace( '\\', '/', $target ) );
        if ( $target === '' || str_starts_with( $target, '/' ) || preg_match( '/^[a-z]:\//i', $target ) ) { return null; }
        $segments = array_values( array_filter( explode( '/', $target ), static fn( string $segment ): bool => $segment !== '' ) );
        if ( $segments === [] || array_intersect( $segments, ['.', '..'] ) !== [] ) { return null; }
        return implode( '/', $segments );
    }

    /**
     * 判断路径是否位于指定目录内
     * @param string $path 待检查路径
     * @param string $directory 限制目录
     * @return bool 是否位于目录内
     */
    private static function pathInside( string $path, string $directory ): bool {
        return str_starts_with( $path, rtrim( $directory, DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR );
    }

    /**
     * 验证验证码
     * @param Request $request 请求对象
     * @param string $code 用户输入的验证码
     * @return bool 验证结果
     */
    public static function verifyCode( Request $request, string $code ): bool {
        $config = plugin( 'ToView' )->config( 'verify' );
        $sessionKey = $config['session_key'];
        $sessionCode = $request->session()->pull( $sessionKey );
        $expiresAt = $request->session()->pull( "{$sessionKey}_expires_at" );
        if ( !is_string( $sessionCode ) || !is_int( $expiresAt ) || time() > $expiresAt ) { return false; }
        return hash_equals( $sessionCode, strtolower( trim( $code ) ) );
    }
}
