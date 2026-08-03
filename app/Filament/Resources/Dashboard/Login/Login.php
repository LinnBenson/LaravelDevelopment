<?php

namespace App\Filament\Resources\Dashboard\Login;

use App\Models\AdminUser;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

/**
 * Login
 * 后台登录页面，使用项目自定义的品牌化登录视图。
 * @package App\Filament\Resources\Dashboard\Login
 */
class Login extends \Filament\Auth\Pages\Login {
    protected string $view = 'Filament.Dashboard.Login.login';

    /**
     * 管理员登录认证。
     * 凭据正确但账号已禁用时返回明确的禁用提示。
     * @return LoginResponse|null 登录响应
     */
    public function authenticate(): ?LoginResponse {
        try {
            return parent::authenticate();
        }catch ( ValidationException $exception ) {
            $data = $this->form->getState();
            $authGuard = Filament::auth();
            $authProvider = $authGuard->getProvider();
            $credentials = $this->getCredentialsFromFormData( $data );
            $adminUser = $authProvider->retrieveByCredentials( $credentials );
            if (
                $adminUser instanceof AdminUser &&
                !$adminUser->status &&
                $authProvider->validateCredentials( $adminUser, $credentials )
            ) {
                throw ValidationException::withMessages( [
                    'data.email' => '此账户已被管理员禁用。',
                ] );
            }
            throw $exception;
        }
    }

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
