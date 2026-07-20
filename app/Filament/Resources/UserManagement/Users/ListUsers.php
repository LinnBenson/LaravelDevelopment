<?php

namespace App\Filament\Resources\UserManagement\Users;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * ListUsers
 * 前台用户列表页面。
 * @package App\Filament\Resources\UserManagement\Users
 */
class ListUsers extends ListRecords {
    protected static string $resource = UserResource::class;

    /**
     * 获取头部操作。
     * 获取列表页面头部操作按钮。
     * @return array<int, CreateAction> 操作按钮
     */
    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
                ->label( '新增用户' ),
        ];
    }
}
