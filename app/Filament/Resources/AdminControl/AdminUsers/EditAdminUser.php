<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use App\Models\AdminUser;
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
     * 编辑自己时保留可用状态，并禁止代理修改自己的用户名。
     * @param array<string, mixed> $data 表单数据
     * @return array<string, mixed> 保存数据
     */
    protected function mutateFormDataBeforeSave( array $data ): array {
        $adminUser = Filament::auth()->user();
        if ( $adminUser instanceof AdminUser && $this->record->getKey() === $adminUser->getKey() ) {
            $data['status'] = $this->record->status;
            if ( $adminUser->level <= (int) config( 'filament.agent', 100 ) ) {
                $data['name'] = $this->record->name;
            }
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
