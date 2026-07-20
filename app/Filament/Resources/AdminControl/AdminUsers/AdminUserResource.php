<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use App\Models\AdminUser;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * AdminUserResource
 * 管理员用户资源。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class AdminUserResource extends Resource {
    protected static ?string $model = AdminUser::class;

    protected static ?string $slug = 'admin-users';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = '管理员控制';

    protected static ?string $navigationLabel = '管理员列表';

    protected static ?string $modelLabel = '管理员';

    protected static ?string $pluralModelLabel = '管理员列表';

    protected static ?int $navigationSort = 1;

    /**
     * 表单配置。
     * 获取管理员用户资源的表单结构。
     * @param Schema $schema 表单结构
     * @return Schema 表单结构
     */
    public static function form( Schema $schema ): Schema {
        return AdminUserForm::configure( $schema );
    }

    /**
     * 表格配置。
     * 获取管理员用户资源的列表表格结构。
     * @param Table $table 表格结构
     * @return Table 表格结构
     */
    public static function table( Table $table ): Table {
        return AdminUsersTable::configure( $table );
    }

    /**
     * 获取管理员列表查询。
     * 只返回当前管理员自己和级别更低的管理员。
     * @return Builder 管理员查询
     */
    public static function getEloquentQuery(): Builder {
        $query = parent::getEloquentQuery();
        $user = Filament::auth()->user();
        if ( ! $user instanceof AdminUser ) { return $query->whereRaw( '1 = 0' ); }
        return $query->where( function ( Builder $query ) use ( $user ): void {
            $query
                ->whereKey( $user->getKey() )
                ->orWhere( 'level', '<', $user->level );
        } );
    }

    /**
     * 获取关联配置。
     * 当前管理员用户资源没有关联管理器。
     * @return array<int, string> 关联配置
     */
    public static function getRelations(): array {
        return [
            //
        ];
    }

    /**
     * 获取页面路由。
     * 获取管理员用户资源的列表、新增和编辑页面路由。
     * @return array<string, mixed> 页面路由
     */
    public static function getPages(): array {
        return [
            'index' => ListAdminUsers::route( '/' ),
            'create' => CreateAdminUser::route( '/create' ),
            'edit' => EditAdminUser::route( '/{record}/edit' ),
        ];
    }
}
