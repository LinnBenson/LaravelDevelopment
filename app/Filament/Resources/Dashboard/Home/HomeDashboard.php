<?php

namespace App\Filament\Resources\Dashboard\Home;

use App\Filament\Concerns\HasNavigationLevel;
use App\Filament\Resources\AdminControl\AdminUsers\AdminUserResource;
use App\Filament\Resources\SystemSettings\ServiceManagement\ServiceManagement;
use App\Filament\Resources\SystemSettings\SystemConfig\SystemConfigPage;
use App\Filament\Resources\UserManagement\NotificationInformation\NotificationInformationResource;
use App\Filament\Resources\UserManagement\Users\UserResource;
use App\Models\AdminUser;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * HomeDashboard
 * 代理与高级管理员共用的后台首页。
 * @package App\Filament\Resources\Dashboard\Home
 */
class HomeDashboard extends Dashboard {
    use HasNavigationLevel;

    protected static string $navigationPermission = 'dashboard';

    protected string $view = 'Filament::Dashboard.Home.home-dashboard';

    /** 获取当前登录管理员。 */
    public function getAdmin(): ?AdminUser {
        $admin = Filament::auth()->user();
        return $admin instanceof AdminUser ? $admin : null;
    }

    /** 判断当前管理员是否为代理。 */
    public function isAgent(): bool {
        return (int) $this->getAdmin()?->level <= (int) config( 'filament.agent', 1000 );
    }

    /** 获取当前管理员名称。 */
    public function getAdminName(): string {
        return (string) ( $this->getAdmin()?->name ?: 'Administrator' );
    }

    /** 获取当前管理员头像。 */
    public function getAdminAvatarUrl(): ?string {
        $avatar = $this->getAdmin()?->avatar;
        if ( blank( $avatar ) || ! Storage::disk( 'public' )->exists( $avatar ) ) { return null; }
        return Storage::disk( 'public' )->url( $avatar );
    }

    /** 根据当前时间获取问候语。 */
    public function getGreeting(): string {
        $hour = (int) now()->format( 'H' );
        if ( $hour < 6 ) { return '夜深了'; }
        if ( $hour < 12 ) { return '早上好'; }
        if ( $hour < 18 ) { return '下午好'; }
        return '晚上好';
    }

    /**
     * 获取当前管理员权限范围内的用户查询。
     * 代理只查询自己名下用户，高级管理员查询全部用户。
     * @return Builder<User> 用户查询
     */
    private function userQuery(): Builder {
        $query = User::query();
        $admin = $this->getAdmin();
        if ( !$admin ) { return $query->whereRaw( '1 = 0' ); }
        if ( $this->isAgent() ) { $query->where( 'agent', $admin->getKey() ); }
        return $query;
    }

    /** 获取当前权限范围内的核心统计。 */
    public function getStats(): array {
        $scope = $this->isAgent() ? '名下' : '全部';
        return [
            ['label' => '用户总数', 'value' => ( clone $this->userQuery() )->count(), 'description' => "{$scope}用户", 'icon' => 'heroicon-o-user-group', 'tone' => 'primary'],
            ['label' => '启用用户', 'value' => ( clone $this->userQuery() )->where( 'status', true )->count(), 'description' => '当前可用账号', 'icon' => 'heroicon-o-check-badge', 'tone' => 'success'],
            ['label' => '停用用户', 'value' => ( clone $this->userQuery() )->where( 'status', false )->count(), 'description' => '当前停用账号', 'icon' => 'heroicon-o-no-symbol', 'tone' => 'danger'],
            ['label' => '本月新增', 'value' => ( clone $this->userQuery() )->whereBetween( 'created_at', [now()->startOfMonth(), now()->endOfMonth()] )->count(), 'description' => now()->format( 'Y 年 m 月' ), 'icon' => 'heroicon-o-chart-bar', 'tone' => 'warning'],
        ];
    }

    /** 获取当前权限范围内最近创建的用户。 */
    public function getRecentUsers(): Collection {
        return $this->userQuery()->latest()->limit( 6 )->get();
    }

    /** 获取当前管理员可访问的快捷入口。 */
    public function getQuickLinks(): array {
        $links = [
            ['label' => '用户列表', 'description' => $this->isAgent() ? '管理我的用户' : '管理全部用户', 'url' => UserResource::getUrl( 'index' ), 'icon' => 'heroicon-o-users', 'visible' => UserResource::canAccess()],
            ['label' => '通知信息', 'description' => '查看通知记录', 'url' => NotificationInformationResource::getUrl( 'index' ), 'icon' => 'heroicon-o-bell', 'visible' => NotificationInformationResource::canAccess()],
            ['label' => '管理员列表', 'description' => '查看后台账号', 'url' => AdminUserResource::getUrl( 'index' ), 'icon' => 'heroicon-o-shield-check', 'visible' => AdminUserResource::canAccess()],
            ['label' => '系统配置', 'description' => '维护应用配置', 'url' => SystemConfigPage::getUrl(), 'icon' => 'heroicon-o-cog-6-tooth', 'visible' => SystemConfigPage::canAccess()],
            ['label' => '服务项管理', 'description' => '查看运行服务', 'url' => ServiceManagement::getUrl(), 'icon' => 'heroicon-o-server-stack', 'visible' => ServiceManagement::canAccess()],
        ];
        return array_values( array_filter( $links, fn ( array $link ): bool => $link['visible'] ) );
    }

    /** 获取当前管理员身份说明。 */
    public function getRoleDescription(): string {
        if ( $this->isAgent() ) { return '代理账号 · 当前数据仅包含您名下的用户'; }
        return '管理员账号 · 当前数据包含全部用户';
    }

    public static function getNavigationLabel(): string { return __( 'filament.navigation.dashboard' ); }
    public function getTitle(): string { return __( 'filament.titles.dashboard' ); }
}
