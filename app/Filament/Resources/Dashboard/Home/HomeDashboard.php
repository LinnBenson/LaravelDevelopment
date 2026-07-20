<?php

namespace App\Filament\Resources\Dashboard\Home;

use App\Filament\Resources\AdminControl\AdminUsers\AdminUserResource;
use App\Filament\Resources\DeveloperCenter\FilamentIcons\FilamentIcons;
use App\Filament\Resources\DeveloperCenter\LogInformation\LogInformation;
use App\Filament\Resources\DeveloperCenter\Readme\Readme;
use App\Filament\Resources\UserManagement\Users\UserResource;
use App\Models\AdminUser;
use App\Models\User;
use Composer\InstalledVersions;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * HomeDashboard
 * 后台首页仪表板。
 * @package App\Filament\Resources\Dashboard\Home
 */
class HomeDashboard extends Dashboard {
    protected static ?string $title = '仪表板';

    protected static ?string $navigationLabel = '仪表板';

    protected string $view = 'Filament.Dashboard.Home.home-dashboard';

    /**
     * 获取当前管理员名称。
     * 返回当前登录管理员的显示名称。
     * @return string 管理员名称
     */
    public function getAdminName(): string {
        return (string) ( Filament::auth()->user()?->name ?: 'Administrator' );
    }

    /**
     * 获取当前管理员头像。
     * 返回当前管理员有效的公开头像地址。
     * @return string|null 头像地址
     */
    public function getAdminAvatarUrl(): ?string {
        $avatar = Filament::auth()->user()?->avatar;
        if ( blank( $avatar ) || ! Storage::disk( 'public' )->exists( $avatar ) ) { return null; }
        return Storage::disk( 'public' )->url( $avatar );
    }

    /**
     * 获取问候语。
     * 根据当前时间返回对应的问候语。
     * @return string 问候语
     */
    public function getGreeting(): string {
        $hour = (int) now()->format( 'H' );
        if ( $hour < 6 ) { return '夜深了'; }
        if ( $hour < 12 ) { return '早上好'; }
        if ( $hour < 18 ) { return '下午好'; }
        return '晚上好';
    }

    /**
     * 获取统计数据。
     * 返回后台首页需要展示的核心数据。
     * @return array<int, array{label: string, value: int, description: string, icon: string}>
     */
    public function getStats(): array {
        return [
            [
                'label' => '用户总数',
                'value' => User::query()->count(),
                'description' => '所有已创建用户',
                'icon' => 'heroicon-o-user-group',
            ],
            [
                'label' => '启用用户',
                'value' => User::query()->where( 'status', true )->count(),
                'description' => '当前正常使用账号',
                'icon' => 'heroicon-o-check-badge',
            ],
            [
                'label' => '管理员',
                'value' => AdminUser::query()->count(),
                'description' => '后台管理员账号',
                'icon' => 'heroicon-o-shield-check',
            ],
            [
                'label' => '日志文件',
                'value' => $this->getLogFileCount(),
                'description' => 'storage/logs 内文件',
                'icon' => 'heroicon-o-document-text',
            ],
        ];
    }

    /**
     * 获取最近用户。
     * 返回最近创建的五个用户。
     * @return Collection<int, User> 最近用户集合
     */
    public function getRecentUsers(): Collection {
        return User::query()->latest()->limit( 5 )->get();
    }

    /**
     * 获取快捷入口。
     * 返回后台常用页面链接。
     * @return array<int, array{label: string, description: string, url: string, icon: string}>
     */
    public function getQuickLinks(): array {
        return [
            [
                'label' => '新增用户',
                'description' => '创建新的前台用户',
                'url' => UserResource::getUrl( 'create' ),
                'icon' => 'heroicon-o-user-plus',
            ],
            [
                'label' => '用户列表',
                'description' => '查看和管理用户',
                'url' => UserResource::getUrl( 'index' ),
                'icon' => 'heroicon-o-users',
            ],
            [
                'label' => '管理员列表',
                'description' => '管理后台账号',
                'url' => AdminUserResource::getUrl( 'index' ),
                'icon' => 'heroicon-o-shield-check',
            ],
            [
                'label' => '日志信息',
                'description' => '检查应用运行日志',
                'url' => LogInformation::getUrl(),
                'icon' => 'heroicon-o-document-magnifying-glass',
            ],
        ];
    }

    /**
     * 获取开发工具入口。
     * 返回开发者中心相关页面链接。
     * @return array<int, array{label: string, url: string}>
     */
    public function getDeveloperLinks(): array {
        return [
            ['label' => 'Filament Icons', 'url' => FilamentIcons::getUrl()],
            ['label' => 'README.md', 'url' => Readme::getUrl()],
        ];
    }

    /**
     * 获取系统信息。
     * 返回当前应用运行环境版本信息。
     * @return array<string, string> 系统信息
     */
    public function getSystemInformation(): array {
        return [
            'Laravel' => app()->version(),
            'Filament' => InstalledVersions::getPrettyVersion( 'filament/filament' ) ?? '--',
            'PHP' => PHP_VERSION,
            '环境' => app()->environment(),
        ];
    }

    /**
     * 获取日志文件数量。
     * 递归统计 storage/logs 目录内的文件。
     * @return int 日志文件数量
     */
    private function getLogFileCount(): int {
        try {
            if ( ! File::isDirectory( storage_path( 'logs' ) ) ) { return 0; }
            return count( File::allFiles( storage_path( 'logs' ) ) );
        }catch ( Throwable ) {
            return 0;
        }
    }
}
