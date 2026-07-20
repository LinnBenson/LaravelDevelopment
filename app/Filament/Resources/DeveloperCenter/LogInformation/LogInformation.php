<?php

namespace App\Filament\Resources\DeveloperCenter\LogInformation;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;
use UnitEnum;

/**
 * LogInformation
 * 开发者中心日志信息页面。
 * @package App\Filament\Resources\DeveloperCenter\LogInformation
 */
class LogInformation extends Page {
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = '开发者中心';

    protected static ?string $navigationLabel = '日志信息';

    protected static ?string $title = '日志信息';

    protected static ?string $slug = 'developer-center/logs';

    protected static ?int $navigationSort = 1;

    protected string $view = 'Filament.DeveloperCenter.LogInformation.log-information';

    public ?string $selectedLog = null;

    public ?string $logContent = null;

    /**
     * 初始化日志页面。
     * 默认打开最近修改的日志文件。
     * @return void
     */
    public function mount(): void {
        $files = $this->getLogFiles();
        if ( $files === [] ) { return; }
        $this->viewLog( $files[0]['path'] );
    }

    /**
     * 获取日志文件列表。
     * 递归返回 storage/logs 目录及其子目录中的普通日志文件。
     * @return array<int, array{name: string, path: string, size: string, modified_at: string}> 日志文件列表
     */
    public function getLogFiles(): array {
        $directory = storage_path( 'logs' );
        if ( ! File::isDirectory( $directory ) ) { return []; }
        $files = [];
        foreach ( File::allFiles( $directory ) as $file ) {
            $relativePath = str_replace( '\\', '/', $file->getRelativePathname() );
            $pathParts = explode( '/', $relativePath );
            $hasHiddenPart = collect( $pathParts )->contains(
                fn ( string $part ): bool => str_starts_with( $part, '.' )
            );
            if ( $hasHiddenPart || $this->resolveLogPath( $relativePath ) === null ) { continue; }
            $files[] = [
                'name' => $relativePath,
                'path' => $relativePath,
                'size' => $this->formatFileSize( $file->getSize() ),
                'modified_at' => date( 'Y.m.d H:i:s', $file->getMTime() ),
                'timestamp' => $file->getMTime(),
            ];
        }
        usort( $files, fn ( array $left, array $right ): int => $right['timestamp'] <=> $left['timestamp'] );
        return array_map( function ( array $file ): array {
            unset( $file['timestamp'] );
            return $file;
        }, $files );
    }

    /**
     * 查看日志文件。
     * 读取指定日志文件尾部最多 200KB 内容。
     * @param string $fileName 日志文件名
     * @return void
     */
    public function viewLog( string $fileName ): void {
        $path = $this->resolveLogPath( $fileName );
        if ( $path === null ) {
            $this->notifyFailure( '日志文件不存在或路径不合法。' );
            return;
        }
        try {
            $this->selectedLog = str_replace( '\\', '/', $fileName );
            $this->logContent = $this->readLastLines( $path );
        }catch ( Throwable ) {
            $this->selectedLog = null;
            $this->logContent = null;
            $this->notifyFailure( '日志文件读取失败，请检查文件权限。' );
        }
    }

    /**
     * 删除日志文件。
     * 删除 storage/logs 目录内指定的日志文件。
     * @param string $fileName 日志文件名
     * @return void
     */
    public function deleteLog( string $fileName ): void {
        $path = $this->resolveLogPath( $fileName );
        if ( $path === null ) {
            $this->notifyFailure( '日志文件不存在或路径不合法。' );
            return;
        }
        try {
            if ( ! File::delete( $path ) ) { throw new RuntimeException( '日志文件删除失败。' ); }
            if ( $this->selectedLog === str_replace( '\\', '/', $fileName ) ) {
                $this->selectedLog = null;
                $this->logContent = null;
                $files = $this->getLogFiles();
                if ( $files !== [] ) { $this->viewLog( $files[0]['path'] ); }
            }
            Notification::make()
                ->title( '日志文件已删除' )
                ->success()
                ->send();
        }catch ( Throwable ) {
            $this->notifyFailure( '日志文件删除失败，请检查文件权限。' );
        }
    }

    /**
     * 删除日志操作。
     * 使用 Filament 确认弹窗并在确认后删除指定日志文件。
     * @return Action 删除日志操作
     */
    public function deleteLogAction(): Action {
        return Action::make( 'deleteLog' )
            ->requiresConfirmation()
            ->modalIcon( Heroicon::OutlinedTrash )
            ->modalHeading( fn ( array $arguments ): string => "删除日志文件 {$arguments['fileName']}？" )
            ->modalDescription( '此操作无法撤销，请确认是否继续。' )
            ->modalSubmitActionLabel( '确认删除' )
            ->modalCancelActionLabel( '取消' )
            ->color( 'danger' )
            ->action( fn ( array $arguments ): mixed => $this->deleteLog( (string) $arguments['fileName'] ) );
    }

    /**
     * 获取页面面包屑。
     * 返回开发者中心日志页面层级。
     * @return array<string> 面包屑列表
     */
    public function getBreadcrumbs(): array {
        return ['开发者中心', '日志信息'];
    }

    /**
     * 解析日志文件路径。
     * 只允许访问 storage/logs 目录内的普通文件。
     * @param string $fileName 日志文件名
     * @return string|null 安全日志路径
     */
    private function resolveLogPath( string $fileName ): ?string {
        $fileName = str_replace( '\\', '/', trim( $fileName ) );
        if ( $fileName === '' || str_starts_with( $fileName, '/' ) ) { return null; }
        $pathParts = explode( '/', $fileName );
        if ( in_array( '..', $pathParts, true ) || in_array( '.', $pathParts, true ) ) { return null; }
        $directory = realpath( storage_path( 'logs' ) );
        if ( $directory === false ) { return null; }
        $path = realpath( "{$directory}/{$fileName}" );
        if ( $path === false || ! is_file( $path ) ) { return null; }
        if ( ! str_starts_with( $path, "{$directory}/" ) ) { return null; }
        return $path;
    }

    /**
     * 读取日志文件最后 200 行。
     * 从文件末尾分块读取，并限制最大读取量避免异常长行占用过多内存。
     * @param string $path 日志文件路径
     * @return string 日志内容
     */
    private function readLastLines( string $path ): string {
        $maxLines = 200;
        $maxBytes = 2 * 1024 * 1024;
        $blockSize = 8192;
        $fileSize = filesize( $path );
        if ( $fileSize === false ) { throw new RuntimeException( '无法获取日志文件大小。' ); }
        $handle = fopen( $path, 'rb' );
        if ( $handle === false ) { throw new RuntimeException( '无法打开日志文件。' ); }
        $position = $fileSize;
        $content = '';
        try {
            while ( $position > 0 && substr_count( $content, "\n" ) <= $maxLines && strlen( $content ) < $maxBytes ) {
                $readBytes = min( $blockSize, $position );
                $position -= $readBytes;
                if ( fseek( $handle, $position ) !== 0 ) { throw new RuntimeException( '无法定位日志文件。' ); }
                $block = fread( $handle, $readBytes );
                if ( $block === false ) { throw new RuntimeException( '无法读取日志文件。' ); }
                $content = "{$block}{$content}";
            }
        }finally {
            fclose( $handle );
        }
        $content = $this->sanitizeUtf8( $content );
        $content = rtrim( $content, "\r\n" );
        if ( $content === '' ) { return '日志文件为空。'; }
        $lines = preg_split( '/\R/', $content );
        if ( $lines === false ) { throw new RuntimeException( '无法解析日志内容。' ); }
        $content = implode( "\n", array_slice( $lines, -$maxLines ) );
        if ( $position > 0 && count( $lines ) < $maxLines ) {
            return "单行日志内容过大，已截取末尾 2MB。\n\n{$content}";
        }
        return $content;
    }

    /**
     * 清理日志内容中的非法 UTF-8 字节。
     * 从文件尾部分块读取时可能截断多字节字符，必须在交给 Livewire 序列化前修复。
     * @param string $content 原始日志内容
     * @return string 可安全进行 JSON 序列化的 UTF-8 内容
     */
    private function sanitizeUtf8( string $content ): string {
        if ( mb_check_encoding( $content, 'UTF-8' ) ) { return $content; }
        if ( function_exists( 'mb_scrub' ) ) { return mb_scrub( $content, 'UTF-8' ); }
        $sanitized = iconv( 'UTF-8', 'UTF-8//IGNORE', $content );
        return $sanitized === false ? '' : $sanitized;
    }

    /**
     * 格式化文件大小。
     * 将字节数转换为易读格式。
     * @param int $bytes 文件字节数
     * @return string 文件大小
     */
    private function formatFileSize( int $bytes ): string {
        if ( $bytes < 1024 ) { return "{$bytes} B"; }
        if ( $bytes < 1024 * 1024 ) { return number_format( $bytes / 1024, 2 ) . ' KB'; }
        if ( $bytes < 1024 * 1024 * 1024 ) { return number_format( $bytes / 1024 / 1024, 2 ) . ' MB'; }
        return number_format( $bytes / 1024 / 1024 / 1024, 2 ) . ' GB';
    }

    /**
     * 发送失败通知。
     * 显示日志文件操作失败信息。
     * @param string $message 失败信息
     * @return void
     */
    private function notifyFailure( string $message ): void {
        Notification::make()
            ->title( $message )
            ->danger()
            ->send();
    }
}
