<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * ListAdminUsers
 * 管理员用户列表页面。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class ListAdminUsers extends ListRecords {
    protected static string $resource = AdminUserResource::class;

    /**
     * 获取头部操作。
     * 获取列表页面头部操作按钮。
     * @return array<int, CreateAction> 操作按钮
     */
    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
                ->label( '新增管理员' ),
        ];
    }
}
