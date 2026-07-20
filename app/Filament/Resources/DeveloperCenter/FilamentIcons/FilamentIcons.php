<?php

namespace App\Filament\Resources\DeveloperCenter\FilamentIcons;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * FilamentIcons
 * 开发者中心 Filament 图标库页面。
 * @package App\Filament\Resources\DeveloperCenter\FilamentIcons
 */
class FilamentIcons extends Page {
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = '开发者中心';

    protected static ?string $navigationLabel = 'Filament Icons';

    protected static ?string $title = 'Filament Icons';

    protected static ?string $slug = 'developer-center/filament-icons';

    protected static ?int $navigationSort = 2;

    protected string $view = 'Filament.DeveloperCenter.FilamentIcons.filament-icons';

    public string $search = '';

    public string $iconStyle = 'all';

    /**
     * 设置图标样式。
     * 切换全部、描边或实心图标分类。
     * @param string $style 图标样式
     * @return void
     */
    public function setIconStyle( string $style ): void {
        if ( ! in_array( $style, ['all', 'outline', 'solid'], true ) ) { return; }
        $this->iconStyle = $style;
    }

    /**
     * 获取图标页面数据。
     * 动态读取当前 Filament 版本提供的全部 Heroicon 枚举。
     * @return array{icons: array<int, array{name: string, value: string, preview: string, usage: string, style: string}>, total: int} 图标页面数据
     */
    public function getIconData(): array {
        $search = mb_strtolower( trim( $this->search ) );
        $icons = array_filter( Heroicon::cases(), function ( Heroicon $icon ) use ( $search ): bool {
            $style = str_starts_with( $icon->name, 'Outlined' ) ? 'outline' : 'solid';
            if ( $this->iconStyle !== 'all' && $style !== $this->iconStyle ) { return false; }
            if ( $search === '' ) { return true; }
            $searchable = mb_strtolower( "{$icon->name} {$icon->value}" );
            return str_contains( $searchable, $search );
        } );
        $total = count( $icons );
        $items = array_map( function ( Heroicon $icon ): array {
            $style = str_starts_with( $icon->name, 'Outlined' ) ? 'outline' : 'solid';
            return [
                'name' => $icon->name,
                'value' => $icon->value,
                'preview' => $icon->getIconForSize( IconSize::Large ),
                'usage' => "Heroicon::{$icon->name}",
                'style' => $style === 'outline' ? '描边' : '实心',
            ];
        }, array_values( $icons ) );
        return [
            'icons' => $items,
            'total' => $total,
        ];
    }

    /**
     * 获取页面面包屑。
     * 返回开发者中心图标页面层级。
     * @return array<string> 面包屑列表
     */
    public function getBreadcrumbs(): array {
        return ['开发者中心', 'Filament Icons'];
    }
}
