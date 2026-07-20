<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * EditAdminUser
 * 编辑管理员用户页面。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class EditAdminUser extends EditRecord {
    protected static string $resource = AdminUserResource::class;

    /**
     * 获取头部操作。
     * 获取编辑页面头部操作按钮。
     * @return array<int, DeleteAction> 操作按钮
     */
    protected function getHeaderActions(): array {
        return [
            DeleteAction::make()
                ->label( '删除' ),
        ];
    }
}
