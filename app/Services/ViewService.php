<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

/**
 * ViewService
 * 视图相关业务服务。
 * @package App\Services
 */
class ViewService {
    /**
     * 渲染框架
     * @return object
     */
    public static function renderFrame(): object {
        // 传递给视图的数据
        return new class {

            /** @var array<string, mixed> 当前主题配置 */
            public array $theme;

            /** @var string 当前语言环境 */
            public string $locale;

            /**
             * 初始化框架视图数据。
             * 根据 Cookie 中的主题名称选择主题，无效时使用 Default 主题。
             */
            public function __construct() {
                // 获取应用主题配置
                $this->theme = ViewService::getTheme();
                // 获取当前语言环境
                $this->locale = str_replace( '_', '-', app()->getLocale() );
            }
        };
    }
    /**
     * 获取当前主题配置。
     * 根据 Cookie 中的主题名称选择主题，无效时使用 Default 主题。
     * @return array<string, mixed> 当前主题配置
     */
    public static function getTheme(): array {
        // 获取应用主题配置
        $themes = setting( 'app.theme', [] );
        $themeName = request()->cookie( 'theme' );
        if ( is_string( $themeName ) && array_key_exists( $themeName, $themes ) ) {
            $theme = $themes[$themeName];
            return is_array( $theme ) ? $theme : [];
        }
        $defaultTheme = $themes['Default'] ?? [];
        return is_array( $defaultTheme ) ? $defaultTheme : [];
    }
}
