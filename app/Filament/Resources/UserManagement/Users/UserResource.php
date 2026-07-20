<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * UserResource
 * 前台用户资源。
 * @package App\Filament\Resources\UserManagement\Users
 */
class UserResource extends Resource {
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = '用户管理';

    protected static ?string $navigationLabel = '用户列表';

    protected static ?string $modelLabel = '用户';

    protected static ?string $pluralModelLabel = '用户列表';

    protected static ?int $navigationSort = 1;

    /**
     * 表单配置。
     * 获取用户资源的表单结构。
     * @param Schema $schema 表单结构
     * @return Schema 表单结构
     */
    public static function form( Schema $schema ): Schema {
        return UserForm::configure( $schema );
    }

    /**
     * 表格配置。
     * 获取用户资源的列表表格结构。
     * @param Table $table 表格结构
     * @return Table 表格结构
     */
    public static function table( Table $table ): Table {
        return UsersTable::configure( $table );
    }

    /**
     * 获取关联配置。
     * 当前用户资源没有关联管理器。
     * @return array<int, string> 关联配置
     */
    public static function getRelations(): array {
        return [];
    }

    /**
     * 获取页面路由。
     * 获取用户列表、新增和编辑页面路由。
     * @return array<string, mixed> 页面路由
     */
    public static function getPages(): array {
        return [
            'index' => ListUsers::route( '/' ),
            'create' => CreateUser::route( '/create' ),
            'edit' => EditUser::route( '/{record}/edit' ),
        ];
    }
}
