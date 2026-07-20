<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use App\Models\AdminUser;

/**
 * AdminUserPolicy
 * 管理员用户访问策略。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class AdminUserPolicy {
    /**
     * 查看管理员列表。
     * 已登录管理员可以查看权限范围内的管理员列表。
     * @param AdminUser $user 当前管理员
     * @return bool 是否允许
     */
    public function viewAny( AdminUser $user ): bool {
        return true;
    }

    /**
     * 查看管理员。
     * 只允许查看自己或级别更低的管理员。
     * @param AdminUser $user 当前管理员
     * @param AdminUser $record 目标管理员
     * @return bool 是否允许
     */
    public function view( AdminUser $user, AdminUser $record ): bool {
        return $this->canManage( $user, $record );
    }

    /**
     * 新增管理员。
     * 等级大于零的管理员可以新增更低级管理员。
     * @param AdminUser $user 当前管理员
     * @return bool 是否允许
     */
    public function create( AdminUser $user ): bool {
        return $user->level > 0;
    }

    /**
     * 编辑管理员。
     * 只允许编辑自己或级别更低的管理员。
     * @param AdminUser $user 当前管理员
     * @param AdminUser $record 目标管理员
     * @return bool 是否允许
     */
    public function update( AdminUser $user, AdminUser $record ): bool {
        return $this->canManage( $user, $record );
    }

    /**
     * 删除管理员。
     * 只允许删除权限范围内的管理员。
     * @param AdminUser $user 当前管理员
     * @param AdminUser $record 目标管理员
     * @return bool 是否允许
     */
    public function delete( AdminUser $user, AdminUser $record ): bool {
        return $this->canManage( $user, $record );
    }

    /**
     * 批量删除管理员。
     * 具体记录仍由删除权限逐条检查。
     * @param AdminUser $user 当前管理员
     * @return bool 是否允许
     */
    public function deleteAny( AdminUser $user ): bool {
        return true;
    }

    /**
     * 判断管理员管理权限。
     * 当前管理员可以管理自己或级别更低的管理员。
     * @param AdminUser $user 当前管理员
     * @param AdminUser $record 目标管理员
     * @return bool 是否允许
     */
    private function canManage( AdminUser $user, AdminUser $record ): bool {
        return $user->is( $record ) || $record->level < $user->level;
    }
}
