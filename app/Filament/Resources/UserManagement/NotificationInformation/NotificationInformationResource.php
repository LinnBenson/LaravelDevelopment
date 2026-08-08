<?php

namespace App\Filament\Resources\UserManagement\NotificationInformation;

use App\Filament\Concerns\HasNavigationLevel;
use App\Models\AdminUser;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use UnitEnum;

/**
 * NotificationInformationResource
 * 后台通知信息资源。
 * @package App\Filament\Resources\UserManagement\NotificationInformation
 */
class NotificationInformationResource extends Resource {
    use HasNavigationLevel;

    protected static string $navigationPermission = 'notification_information';

    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $slug = 'notification-information';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?int $navigationSort = 20;

    /**
     * 配置通知信息表格。
     * @param Table $table 表格对象
     * @return Table 通知信息表格
     */
    public static function table( Table $table ): Table {
        return NotificationInformationTable::configure( $table );
    }

    /**
     * 获取通知信息查询。
     * 管理员通知限制为当前管理员自己及低级管理员；代理只能查看自己名下用户通知。
     * @return Builder 已应用数据权限的查询
     */
    public static function getEloquentQuery(): Builder {
        $query = parent::getEloquentQuery()->with( 'notifiable' );
        $adminUser = Filament::auth()->user();
        if ( !$adminUser instanceof AdminUser ) { return $query->whereRaw( '1 = 0' ); }
        return $query->where( function ( Builder $query ) use ( $adminUser ): void {
            $query
                ->where( function ( Builder $query ) use ( $adminUser ): void {
                    $query
                        ->where( 'notifiable_type', AdminUser::class )
                        ->whereIn( 'notifiable_id', AdminUser::query()
                            ->select( 'id' )
                            ->where( function ( Builder $query ) use ( $adminUser ): void {
                                $query
                                    ->whereKey( $adminUser->getKey() )
                                    ->orWhere( 'level', '<', $adminUser->level );
                            } ) );
                } )
                ->orWhere( function ( Builder $query ) use ( $adminUser ): void {
                    $query->where( 'notifiable_type', User::class );
                    if ( $adminUser->level <= (int) config( 'filament.agent', 100 ) ) {
                        $query->whereIn( 'notifiable_id', User::query()
                            ->select( 'id' )
                            ->where( 'agent', $adminUser->getKey() ) );
                    }
                } );
        } );
    }

    /**
     * 获取页面路由。
     * @return array<string, mixed> 页面路由
     */
    public static function getPages(): array {
        return [
            'index' => ListNotificationInformation::route( '/' ),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function getNavigationLabel(): string { return __( 'filament.navigation.notification_information' ); }
    public static function getModelLabel(): string { return __( 'filament.models.notification' ); }
    public static function getPluralModelLabel(): string { return __( 'filament.models.notifications' ); }
    public static function getNavigationGroup(): string|UnitEnum|null { return __( 'filament.groups.user' ); }
}
