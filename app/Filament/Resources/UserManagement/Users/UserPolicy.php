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
        return $adminUser->status;
    }

    public function create( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }

    public function update( AdminUser $adminUser, User $user ): bool {
        return $adminUser->status;
    }

    public function delete( AdminUser $adminUser, User $user ): bool {
        return $adminUser->status;
    }

    public function deleteAny( AdminUser $adminUser ): bool {
        return $adminUser->status;
    }
}
