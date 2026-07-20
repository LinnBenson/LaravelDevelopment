<?php

namespace App\Filament\Resources\Dashboard\Login;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Login
 * 后台登录页面，使用项目自定义的品牌化登录视图。
 * @package App\Filament\Resources\Dashboard\Login
 */
class Login extends \Filament\Auth\Pages\Login {
    protected string $view = 'Filament.Dashboard.Login.login';

    /**
     * 隐藏默认标题。
     * 标题由自定义登录视图统一呈现。
     * @return string|Htmlable|null 页面标题
     */
    public function getHeading(): string | Htmlable | null {
        return null;
    }

    /**
     * 隐藏默认品牌标识。
     * 品牌区域由自定义登录视图统一呈现。
     * @return bool 是否显示默认品牌标识
     */
    public function hasLogo(): bool {
        return false;
    }
}
