<?php

namespace App\Plugins\ToView\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller {
    /**
     * 输出插件资源
     * 从插件 assets 目录读取并返回公开文件。
     * @param string $path 资源相对路径
     * @return BinaryFileResponse|Response
     */
    public function show( string $path ): BinaryFileResponse|Response {
        $plugin = plugin( 'ToView' );
        if ( $plugin === null ) {
            abort( 404 );
        }
        $assetDirectory = realpath( "{$plugin->path}Assets" );
        if ( $assetDirectory === false ) {
            abort( 404 );
        }
        $file = realpath( "{$assetDirectory}/{$path}" );
        // 防止使用 ../ 读取插件目录外的文件
        if (
            $file === false ||
            !str_starts_with( $file, "{$assetDirectory}/" ) ||
            !is_file( $file ) ||
            !is_readable( $file )
        ) {
            abort( 404 );
        }
        return response()->file( $file, [
            'Content-Type' => 'text/css; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ] );
    }
}