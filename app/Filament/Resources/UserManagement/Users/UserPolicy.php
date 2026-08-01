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
        return $this->canManage( $adminUser, $user );
    }

    public function delete( AdminUser $adminUser, User $user ): bool {
        return $this->canManage( $adminUser, $user );
    }

    public function deleteAny( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }

    /**
     * 判断管理员是否可以管理用户。
     * 等级大于等于 100 时可以管理全部用户，否则只能管理自己名下的用户。
     * @param AdminUser $adminUser 当前管理员
     * @param User $user 目标用户
     * @return bool 是否允许管理
     */
    private function canManage( AdminUser $adminUser, User $user ): bool {
        return $adminUser->status && (
            $adminUser->level >= 100 || $user->agent === $adminUser->getKey()
        );
    }
}
