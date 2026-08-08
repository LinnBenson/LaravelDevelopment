<?php

namespace App\Filament\Resources\UserManagement\NotificationInformation;

use App\Models\AdminUser;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * NotificationInformationTable
 * 后台通知信息表格。
 * @package App\Filament\Resources\UserManagement\NotificationInformation
 */
class NotificationInformationTable {
    /**
     * 配置通知信息表格。
     * @param Table $table 表格对象
     * @return Table 通知信息表格
     */
    public static function configure( Table $table ): Table {
        return $table
            ->defaultSort( 'created_at', 'desc' )
            ->columns( [
                TextColumn::make( 'notifiable_type' )
                    ->label( '接收者类型' )
                    ->formatStateUsing( fn ( string $state ): string => match ( $state ) {
                        AdminUser::class => '管理员',
                        User::class => '用户',
                        default => '其它',
                    } )
                    ->badge()
                    ->color( fn ( string $state ): string => $state === AdminUser::class ? 'warning' : 'primary' ),
                TextColumn::make( 'notifiable_id' )
                    ->label( 'UID' )
                    ->sortable()
                    ->searchable(),
                TextColumn::make( 'notifiable' )
                    ->label( '接收者' )
                    ->getStateUsing( fn ( DatabaseNotification $record ): string => self::getNotifiableName( $record ) ),
                TextColumn::make( 'data.title' )
                    ->label( '标题' )
                    ->limit( 20 )
                    ->lineClamp( 1 )
                    ->tooltip( function ( DatabaseNotification $record ): ?string {
                        $title = (string) data_get( $record->data, 'title', '' );
                        return mb_strlen( $title ) > 20 ? $title : null;
                    } )
                    ->action( self::viewAction( 'viewNotificationFromTitle' ) ),
                TextColumn::make( 'data.body' )
                    ->label( '内容' )
                    ->limit( 30 )
                    ->lineClamp( 2 )
                    ->tooltip( function ( DatabaseNotification $record ): ?string {
                        $body = (string) data_get( $record->data, 'body', '' );
                        return mb_strlen( $body ) > 30 ? Str::limit( $body, 300 ) : null;
                    } )
                    ->action( self::viewAction( 'viewNotificationFromBody' ) ),
                TextColumn::make( 'read_at' )
                    ->label( '状态' )
                    ->formatStateUsing( fn ( mixed $state ): string => $state === null ? '未读' : '已读' )
                    ->badge()
                    ->color( fn ( mixed $state ): string => $state === null ? 'danger' : 'success' ),
                TextColumn::make( 'read_at_time' )
                    ->label( '阅读时间' )
                    ->getStateUsing( fn ( DatabaseNotification $record ): string => $record->read_at?->format( 'Y.m.d H:i:s' ) ?? '-' )
                    ->toggleable( isToggledHiddenByDefault: true ),
                TextColumn::make( 'created_at' )
                    ->label( '发送时间' )
                    ->dateTime( 'Y.m.d H:i:s' )
                    ->sortable(),
            ] )
            ->filters( [
                SelectFilter::make( 'notifiable_type' )
                    ->label( '接收者类型' )
                    ->options( [
                        AdminUser::class => '管理员',
                        User::class => '用户',
                    ] )
                    ->native( false ),
                TernaryFilter::make( 'read_at' )
                    ->label( '阅读状态' )
                    ->nullable()
                    ->trueLabel( '已读' )
                    ->falseLabel( '未读' ),
                Filter::make( 'created_at' )
                    ->label( '发送时间范围' )
                    ->schema( [
                        DatePicker::make( 'from' )
                            ->label( '开始日期' )
                            ->displayFormat( 'Y.m.d' )
                            ->native( false ),
                        DatePicker::make( 'until' )
                            ->label( '结束日期' )
                            ->displayFormat( 'Y.m.d' )
                            ->native( false ),
                    ] )
                    ->columns( 2 )
                    ->query( function ( Builder $query, array $data ): Builder {
                        return $query
                            ->when(
                                filled( $data['from'] ?? null ),
                                fn ( Builder $query ): Builder => $query->where(
                                    'created_at',
                                    '>=',
                                    Carbon::parse( $data['from'] )->startOfDay(),
                                ),
                            )
                            ->when(
                                filled( $data['until'] ?? null ),
                                fn ( Builder $query ): Builder => $query->where(
                                    'created_at',
                                    '<=',
                                    Carbon::parse( $data['until'] )->endOfDay(),
                                ),
                            );
                    } )
                    ->indicateUsing( function ( array $data ): array {
                        $indicators = [];
                        if ( filled( $data['from'] ?? null ) ) {
                            $indicators[] = Indicator::make(
                                '发送日期从 '.Carbon::parse( $data['from'] )->format( 'Y.m.d' )
                            )->removeField( 'from' );
                        }
                        if ( filled( $data['until'] ?? null ) ) {
                            $indicators[] = Indicator::make(
                                '发送日期至 '.Carbon::parse( $data['until'] )->format( 'Y.m.d' )
                            )->removeField( 'until' );
                        }
                        return $indicators;
                    } ),
            ] )
            ->filtersFormColumns( 1 )
            ->filtersFormWidth( Width::TwoExtraLarge )
            ->recordActions( [
                ActionGroup::make( [
                    self::viewAction( 'viewNotification' ),
                    DeleteAction::make()
                        ->label( __( 'filament.actions.delete' ) )
                        ->authorize( fn ( DatabaseNotification $record ): bool => self::canAccessNotification( $record ) )
                        ->using( fn ( DatabaseNotification $record ): bool => DB::transaction(
                            fn (): bool => (bool) $record->delete()
                        ) ),
                ] ),
            ] )
            ->recordActionsColumnLabel( '操作' );
    }

    /**
     * 创建查看通知操作。
     * 使用只读弹窗展示通知完整内容。
     * @param string $name 操作名称
     * @return Action 查看通知操作
     */
    private static function viewAction( string $name ): Action {
        return Action::make( $name )
            ->label( __( 'filament.actions.view' ) )
            ->icon( Heroicon::OutlinedEye )
            ->color( 'primary' )
            ->authorize( fn ( DatabaseNotification $record ): bool => self::canAccessNotification( $record ) )
            ->modalHeading( fn ( DatabaseNotification $record ): string => (string) ( $record->data['title'] ?? '通知详情' ) )
            ->modalContent( fn ( DatabaseNotification $record ) => view(
                'Filament::UserManagement.NotificationInformation.notification-details',
                [
                    'record' => $record,
                    'notifiableName' => self::getNotifiableName( $record ),
                ]
            ) )
            ->modalWidth( '4xl' )
            ->extraModalWindowAttributes( ['class' => 'notification-information-details-modal'] )
            ->modalSubmitAction( false )
            ->modalCancelActionLabel( __( 'filament.actions.close' ) );
    }

    /**
     * 判断当前管理员是否可以访问通知。
     * 重用资源查询权限，防止通过伪造行操作访问越权通知。
     * @param DatabaseNotification $notification 通知记录
     * @return bool 是否允许访问
     */
    private static function canAccessNotification( DatabaseNotification $notification ): bool {
        return NotificationInformationResource::getEloquentQuery()
            ->whereKey( $notification->getKey() )
            ->exists();
    }

    /**
     * 获取通知接收者名称。
     * @param DatabaseNotification $notification 通知记录
     * @return string 接收者名称
     */
    private static function getNotifiableName( DatabaseNotification $notification ): string {
        $notifiable = $notification->notifiable;
        if ( $notifiable instanceof AdminUser ) { return $notifiable->name; }
        if ( $notifiable instanceof User ) {
            return filled( $notifiable->nickname )
                ? $notifiable->nickname
                : ( $notifiable->name ?: "UID {$notifiable->getKey()}" );
        }
        return '接收者已删除';
    }
}
