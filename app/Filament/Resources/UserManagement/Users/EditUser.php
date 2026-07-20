<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * EditUser
 * 编辑前台用户页面。
 * @package App\Filament\Resources\UserManagement\Users
 */
class EditUser extends EditRecord {
    protected static string $resource = UserResource::class;

    /**
     * 处理表单填充数据。
     * 将已存储的电话号码拆分为区号和本地号码。
     * @param array<string, mixed> $data 记录数据
     * @return array<string, mixed> 表单填充数据
     */
    protected function mutateFormDataBeforeFill( array $data ): array {
        $phone = User::splitPhone( isset( $data['phone'] ) ? (string) $data['phone'] : null );
        $data['phone_area_code'] = $phone['area_code'];
        $data['phone'] = $phone['number'];
        return $data;
    }

    /**
     * 处理保存数据。
     * 将区号与本地号码组合为数据库存储格式。
     * @param array<string, mixed> $data 表单数据
     * @return array<string, mixed> 处理后的表单数据
     */
    protected function mutateFormDataBeforeSave( array $data ): array {
        $data['phone'] = User::formatPhoneForStorage(
            isset( $data['phone_area_code'] ) ? (string) $data['phone_area_code'] : null,
            isset( $data['phone'] ) ? (string) $data['phone'] : null,
        );
        unset( $data['phone_area_code'] );
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
