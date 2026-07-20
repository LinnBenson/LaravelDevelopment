<?php

namespace App\Filament\Resources\DeveloperCenter\BootstrapIcons;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * BootstrapIcons
 * 开发者中心 Bootstrap Icons 图标库页面。
 * @package App\Filament\Resources\DeveloperCenter\BootstrapIcons
 */
class BootstrapIcons extends Page {
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = '开发者中心';

    protected static ?string $navigationLabel = 'Bootstrap Icons';

    protected static ?string $title = 'Bootstrap Icons';

    protected static ?string $slug = 'developer-center/bootstrap-icons';

    protected static ?int $navigationSort = 3;

    protected string $view = 'Filament.DeveloperCenter.BootstrapIcons.bootstrap-icons';

    public string $search = '';

    public string $iconStyle = 'all';

    /**
     * 设置图标样式。
     * 切换全部、常规或实心图标分类。
     * @param string $style 图标样式
     * @return void
     */
    public function setIconStyle( string $style ): void {
        if ( ! in_array( $style, ['all', 'regular', 'fill'], true ) ) { return; }
        $this->iconStyle = $style;
    }

    /**
     * 获取图标页面数据。
     * 从 Bootstrap Icons CSS 文件动态解析全部图标类名。
     * @return array{icons: array<int, array{name: string, class: string, style: string}>, total: int} 图标页面数据
     */
    public function getIconData(): array {
        $search = mb_strtolower( trim( $this->search ) );
        $icons = array_filter( $this->getAvailableIcons(), function ( string $name ) use ( $search ): bool {
            $style = str_ends_with( $name, '-fill' ) ? 'fill' : 'regular';
            if ( $this->iconStyle !== 'all' && $style !== $this->iconStyle ) { return false; }
            if ( $search === '' ) { return true; }
            return str_contains( mb_strtolower( "{$name} bi-{$name}" ), $search );
        } );
        $total = count( $icons );
        $items = array_map( function ( string $name ): array {
            $isFilled = str_ends_with( $name, '-fill' );
            return [
                'name' => $name,
                'class' => "bi bi-{$name}",
                'style' => $isFilled ? '实心' : '常规',
            ];
        }, array_values( $icons ) );
        return [
            'icons' => $items,
            'total' => $total,
        ];
    }

    /**
     * 获取 Bootstrap 图标类名。
     * 读取项目公开 CSS 中全部 bi-* 伪元素选择器。
     * @return array<int, string> 图标类名列表
     */
    private function getAvailableIcons(): array {
        $cssPath = public_path( 'css/BootstrapIcons.css' );
        if ( ! is_readable( $cssPath ) ) { return []; }
        $css = file_get_contents( $cssPath );
        if ( $css === false ) { return []; }
        preg_match_all( '/\\.bi-([a-z0-9-]+)::before/', $css, $matches );
        $icons = array_values( array_unique( $matches[1] ?? [] ) );
        sort( $icons, SORT_NATURAL );
        return $icons;
    }

    /**
     * 获取页面面包屑。
     * 返回开发者中心图标页面层级。
     * @return array<string> 面包屑列表
     */
    public function getBreadcrumbs(): array {
        return ['开发者中心', 'Bootstrap Icons'];
    }
}
