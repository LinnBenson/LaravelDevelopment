<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

/**
 * CreateUser
 * 新增前台用户页面。
 * @package App\Filament\Resources\UserManagement\Users
 */
class CreateUser extends CreateRecord {
    protected static string $resource = UserResource::class;

    /**
     * 处理新增数据。
     * 将区号与本地号码组合为数据库存储格式。
     * @param array<string, mixed> $data 表单数据
     * @return array<string, mixed> 处理后的表单数据
     */
    protected function mutateFormDataBeforeCreate( array $data ): array {
        $data['phone'] = User::formatPhoneForStorage(
            isset( $data['phone_area_code'] ) ? (string) $data['phone_area_code'] : null,
            isset( $data['phone'] ) ? (string) $data['phone'] : null,
        );
        unset( $data['phone_area_code'] );
        return $data;
    }
}
