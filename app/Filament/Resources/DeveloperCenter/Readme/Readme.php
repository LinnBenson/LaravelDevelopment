<?php

namespace App\Filament\Resources\DeveloperCenter\Readme;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;
use UnitEnum;

/**
 * Readme
 * 开发者中心项目说明页面。
 * @package App\Filament\Resources\DeveloperCenter\Readme
 */
class Readme extends Page {
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = '开发者中心';

    protected static ?string $navigationLabel = 'README.md';

    protected static ?string $title = 'README.md';

    protected static ?string $slug = 'developer-center/readme';

    protected static ?int $navigationSort = 4;

    protected string $view = 'Filament.DeveloperCenter.Readme.readme';

    /**
     * 获取 README 格式化内容。
     * 读取项目根目录 README.md，并转换为安全 HTML。
     * @return HtmlString 格式化内容
     */
    public function getReadmeHtml(): HtmlString {
        $path = base_path( 'README.md' );
        if ( ! File::isFile( $path ) || ! File::isReadable( $path ) ) {
            return new HtmlString( '<p>根目录 README.md 不存在或无法读取。</p>' );
        }
        try {
            $markdown = File::get( $path );
            $html = Str::markdown( $markdown, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 100,
            ] );
            return new HtmlString( $html );
        }catch ( Throwable ) {
            return new HtmlString( '<p>README.md 读取或解析失败，请检查文件权限和内容格式。</p>' );
        }
    }

    /**
     * 获取 README 修改时间。
     * 返回项目说明文件最后修改时间。
     * @return string 修改时间
     */
    public function getReadmeModifiedAt(): string {
        $path = base_path( 'README.md' );
        if ( ! File::isFile( $path ) ) { return '--'; }
        try {
            return date( 'Y.m.d H:i:s', File::lastModified( $path ) );
        }catch ( Throwable ) {
            return '--';
        }
    }

    /**
     * 获取页面面包屑。
     * 返回开发者中心 README 页面层级。
     * @return array<string> 面包屑列表
     */
    public function getBreadcrumbs(): array {
        return ['开发者中心', 'README.md'];
    }
}
