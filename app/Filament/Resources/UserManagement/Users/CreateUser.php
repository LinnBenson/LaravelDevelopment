<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\AdminUser;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

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
        $adminUser = Filament::auth()->user();
        if ( $adminUser instanceof AdminUser && $adminUser->level <= (int) config( 'filament.agent', 100 ) ) {
            $data['agent'] = $adminUser->getKey();
        }else {
            $agent = (int) ( $data['agent'] ?? 0 );
            $agentExists = $agent === 0 || AdminUser::query()
                ->whereKey( $agent )
                ->where( 'level', '<=', (int) config( 'filament.agent', 100 ) )
                ->exists();
            if ( !$agentExists ) {
                throw ValidationException::withMessages( ['agent' => '选择的代理不存在或级别不符合要求。'] );
            }
            $data['agent'] = $agent;
        }
        $data['phone'] = User::formatPhoneForStorage(
            isset( $data['phone_area_code'] ) ? (string) $data['phone_area_code'] : null,
            isset( $data['phone'] ) ? (string) $data['phone'] : null,
        );
        unset( $data['phone_area_code'] );
        return $data;
    }
}
