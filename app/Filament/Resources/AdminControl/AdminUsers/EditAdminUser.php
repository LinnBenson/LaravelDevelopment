<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

/**
 * EditAdminUser
 * 编辑管理员用户页面。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class EditAdminUser extends EditRecord {
    protected static string $resource = AdminUserResource::class;

    /**
     * 处理保存数据。
     * 编辑当前登录管理员时强制保留原有可用状态。
     * @param array<string, mixed> $data 表单数据
     * @return array<string, mixed> 保存数据
     */
    protected function mutateFormDataBeforeSave( array $data ): array {
        if ( $this->record->getKey() === Filament::auth()->id() ) {
            $data['status'] = $this->record->status;
        }
        return $data;
    }

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
