<?php

namespace App\Plugins\ToView\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * 上传控制器
 */
class UploadController extends Controller {
    /**
     * 获取上传配置
     * @return JsonResponse 上传配置
     */
    public function uploadConfig(): JsonResponse {
        $config = plugin( 'ToView' )->config( 'upload' );
        return echoJson( 0, $config );
    }
    /**
     * 上传文件
     * 接收 file 或 file[] 字段，验证后将一个或多个文件保存到配置目录。
     * @param Request $request 上传请求
     * @return JsonResponse 上传结果
     */
    public function upload( Request $request ): JsonResponse {
        $uploadConfig = plugin( 'ToView' )->config( 'upload' );
        $files = $this->flattenFiles( $request->file( 'file' ) );
        if ( $files === [] ) { return echoJson( 2, ['base.error.input'], 422 ); }
        foreach ( $files as $file ) {
            if ( !$file->isValid() ) { return echoJson( 2, ['base.error.input'], 422 ); }
            $size = (int) $file->getSize();
            if ( $size <= 0 || $size > $uploadConfig['size'] ) {
                return echoJson( 2, ['validation.max.file', [ 'attribute' => 'File', 'max' => ( $uploadConfig['size'] / 1024 )]], 422 );
            }
            $extension = strtolower( $file->getClientOriginalExtension() );
            $allowedMimes = $uploadConfig['ext'][$extension] ?? [];
            if ( $allowedMimes === [] ) { return echoJson( 3, ['base.error.illegal'], 422 ); }
            if ( !in_array( strtolower( (string) $file->getMimeType() ), $allowedMimes, true ) ) {
                return echoJson( 3, ['base.error.illegal'], 422 );
            }
        }
        $directory = storage_path( 'app/'.trim( $uploadConfig['path'], '/' ) );
        if ( !is_dir( $directory ) && !mkdir( $directory, 0750, true ) && !is_dir( $directory ) ) {
            return echoJson( 2, ['base.error.500'], 500 );
        }
        $storedFiles = [];
        $links = [];
        try {
            foreach ( $files as $file ) {
                $extension = strtolower( $file->getClientOriginalExtension() );
                $fileName = Str::uuid()->toString().".{$extension}";
                $file->move( $directory, $fileName );
                $storedFiles[] = "{$directory}/{$fileName}";
                $links[] = route( 'plugins.to-view.upload.tmp', ['file' => $fileName], false );
            }
        }catch ( Throwable $error ) {
            foreach ( $storedFiles as $storedFile ) {
                if ( is_file( $storedFile ) ) { unlink( $storedFile ); }
            }
            report( $error );
            return echoJson( 2, ['base.error.500'], 500 );
        }
        return echoJson( true, $links );
    }
    /**
     * 读取临时文件
     * 读取上传目录中指定的文件，文件存在即可公开访问。
     * @param string $file 文件名
     * @return BinaryFileResponse|JsonResponse 文件响应或错误响应
     */
    public function tmp( string $file ): BinaryFileResponse|JsonResponse {
        $uploadConfig = plugin( 'ToView' )->config( 'upload' );
        if ( $file === '' || basename( $file ) !== $file ) { return echoJson( false, 'File not found.', 404 ); }
        $path = storage_path( 'app/'.trim( $uploadConfig['path'], '/' )."/{$file}" );
        if ( !is_file( $path ) ) { return echoJson( 2, ['base.error.404'], 404 ); }
        $mime = mime_content_type( $path ) ?: 'application/octet-stream';
        $inline = ( str_starts_with( $mime, 'image/' ) && $mime !== 'image/svg+xml' ) || str_starts_with( $mime, 'audio/' ) || str_starts_with( $mime, 'video/' );
        return response()->file( $path, [
            'Content-Disposition' => ( $inline ? 'inline' : 'attachment' ).'; filename="'.addcslashes( $file, '"\\' ).'"',
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ] );
    }

    /**
     * 整理上传文件
     * 将单文件或嵌套的多文件字段整理为一维数组。
     * @param UploadedFile|array<array-key, mixed>|null $files 上传文件
     * @return array<int, UploadedFile> 上传文件列表
     */
    private function flattenFiles( UploadedFile|array|null $files ): array {
        if ( $files instanceof UploadedFile ) { return [$files]; }
        $result = [];
        foreach ( $files ?? [] as $file ) {
            if ( $file instanceof UploadedFile ) {
                $result[] = $file;
            }elseif ( is_array( $file ) ) {
                $result = [...$result, ...$this->flattenFiles( $file )];
            }
        }
        return $result;
    }
}
