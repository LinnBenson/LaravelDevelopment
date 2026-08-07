<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\AdminUser;
use App\Models\User;

/**
 * UserPolicy
 * 前台用户后台管理策略。
 * @package App\Filament\Resources\UserManagement\Users
 */
class UserPolicy {
    public function viewAny( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }

    public function view( AdminUser $adminUser, User $user ): bool {
        return $this->canManage( $adminUser, $user );
    }

    public function create( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }

    public function update( AdminUser $adminUser, User $user ): bool {
        return $this->canModify( $adminUser, $user );
    }

    public function delete( AdminUser $adminUser, User $user ): bool {
        return $this->canModify( $adminUser, $user );
    }

    public function deleteAny( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }

    /**
     * 判断管理员是否可以管理用户。
     * 超过代理等级时可以管理全部用户，否则只能管理自己名下的用户。
     * @param AdminUser $adminUser 当前管理员
     * @param User $user 目标用户
     * @return bool 是否允许管理
     */
    private function canManage( AdminUser $adminUser, User $user ): bool {
        return $adminUser->status && (
            $adminUser->level > (int) config( 'filament.agent', 100 ) ||
            $user->agent === $adminUser->getKey()
        );
    }

    /**
     * 判断管理员是否可以修改用户。
     * 高级管理员可以修改权限范围内的用户，代理不能修改 User 及以上等级的用户。
     * @param AdminUser $adminUser 当前管理员
     * @param User $user 目标用户
     * @return bool 是否允许修改
     */
    private function canModify( AdminUser $adminUser, User $user ): bool {
        if ( !$this->canManage( $adminUser, $user ) ) { return false; }
        if ( $adminUser->level > (int) config( 'filament.agent', 100 ) ) { return true; }
        return $user->level < User::LEVEL_USER;
    }
}
