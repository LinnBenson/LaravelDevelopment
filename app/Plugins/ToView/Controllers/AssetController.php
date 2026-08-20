<?php

namespace App\Plugins\ToView\Controllers;

use Illuminate\Http\Request;
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

    /**
     * 获取验证码图片
     * 生成验证码图片并将验证码保存到当前会话。
     * @param Request $request 请求对象
     * @return BinaryFileResponse|Response 验证码图片响应
     */
    public function verify( Request $request ): BinaryFileResponse|Response {
        if ( !function_exists( 'imagecreatetruecolor' ) ) {
            return response( 'GD extension is unavailable.', 503, ['Content-Type' => 'text/plain; charset=utf-8'] );
        }
        $plugin = plugin( 'ToView' );
        $config = $plugin->config( 'verify' );
        $characters = $config['characters'];
        $code = '';
        for ( $index = 0; $index < $config['length']; $index++ ) {
            $code .= $characters[random_int( 0, strlen( $characters ) - 1 )];
        }
        $request->session()->put( [
            $config['session_key'] => strtolower( $code ),
            "{$config['session_key']}_expires_at" => now()->addSeconds( $config['expire'] )->timestamp,
        ] );
        $width = $config['width'];
        $height = $config['height'];
        $image = imagecreatetruecolor( $width, $height );
        if ( $image === false ) {
            return response( 'Unable to create verification image.', 500, ['Content-Type' => 'text/plain; charset=utf-8'] );
        }
        $background = imagecolorallocate( $image, ...$config['background'] );
        imagefill( $image, 0, 0, $background );
        for ( $index = 0; $index < $config['lines']; $index++ ) {
            $lineColor = imagecolorallocate( $image, random_int( 120, 210 ), random_int( 120, 210 ), random_int( 120, 210 ) );
            imageline( $image, random_int( 0, $width - 1 ), random_int( 0, $height - 1 ), random_int( 0, $width - 1 ), random_int( 0, $height - 1 ), $lineColor );
        }
        for ( $index = 0; $index < $config['noise']; $index++ ) {
            $noiseColor = imagecolorallocate( $image, random_int( 100, 220 ), random_int( 100, 220 ), random_int( 100, 220 ) );
            imagesetpixel( $image, random_int( 0, $width - 1 ), random_int( 0, $height - 1 ), $noiseColor );
        }
        $font = "{$plugin->path}{$config['font']}";
        $characterWidth = $width / $config['length'];
        foreach ( str_split( $code ) as $index => $character ) {
            $textColor = imagecolorallocate( $image, random_int( 25, 100 ), random_int( 25, 100 ), random_int( 25, 100 ) );
            $x = (int) ( ( $index * $characterWidth ) + ( $characterWidth * 0.2 ) );
            imagettftext( $image, random_int( ...$config['font_size'] ), random_int( ...$config['angle'] ), $x, random_int( $height - 14, $height - 5 ), $textColor, $font, $character );
        }
        ob_start();
        imagepng( $image );
        $content = ob_get_clean();
        imagedestroy( $image );
        if ( !is_string( $content ) ) {
            return response( 'Unable to create verification image.', 500, ['Content-Type' => 'text/plain; charset=utf-8'] );
        }
        return response( $content, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ] );
    }
}
