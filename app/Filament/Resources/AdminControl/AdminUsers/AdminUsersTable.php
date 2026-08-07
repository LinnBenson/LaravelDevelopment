<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use App\Filament\Config\DatabaseNotificationConfig;
use App\Models\AdminUser;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

/**
 * AdminUsersTable
 * 管理员用户列表。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class AdminUsersTable {
    /**
     * 配置表格。
     * 配置管理员用户列表字段、筛选和操作。
     * @param Table $table 表格结构
     * @return Table 表格结构
     */
    public static function configure( Table $table ): Table {
        return $table
            ->columns( [
                TextColumn::make( 'id' )
                    ->label( 'UID' )
                    ->sortable()
                    ->searchable(),
                ViewColumn::make( 'name' )
                    ->label( '用户名' )
                    ->view( 'Filament::AdminControl.AdminUsers.admin-user-name' )
                    ->searchable(),
                TextColumn::make( 'email' )
                    ->label( '邮箱' )
                    ->searchable(),
                ToggleColumn::make( 'status' )
                    ->label( '状态' )
                    ->onColor( 'success' )
                    ->offColor( 'danger' )
                    ->onIcon( 'heroicon-m-check' )
                    ->offIcon( 'heroicon-m-x-mark' )
                    ->disabled( function ( AdminUser $record ): bool {
                        $adminUser = auth( 'admin' )->user();
                        return !$adminUser instanceof AdminUser ||
                            $adminUser->is( $record ) ||
                            Gate::forUser( $adminUser )->denies( 'update', $record );
                    } )
                    ->afterStateUpdated( function ( bool $state, AdminUser $record ): void {
                        $status = $state ? '启用' : '禁用';
                        Notification::make()
                            ->title( '管理员状态修改成功' )
                            ->body( "管理员 {$record->name} 已{$status}。" )
                            ->success()
                            ->send();
                    } ),
                TextColumn::make( 'level' )
                    ->label( '级别' )
                    ->numeric()
                    ->sortable(),
                TextColumn::make( 'created_at' )
                    ->label( '创建时间' )
                    ->dateTime( 'Y.m.d H:i:s' )
                    ->sortable(),
                TextColumn::make( 'updated_at' )
                    ->label( '更新时间' )
                    ->dateTime( 'Y.m.d H:i:s' )
                    ->sortable()
                    ->toggleable( isToggledHiddenByDefault: true ),
            ] )
            ->filters( [
                TernaryFilter::make( 'status' )
                    ->label( '状态' ),
            ] )
            ->recordActions( [
                ActionGroup::make( [
                    self::sendMessageAction(),
                    EditAction::make()
                        ->label( __( 'filament.actions.edit' ) ),
                    DeleteAction::make()
                        ->label( __( 'filament.actions.delete' ) ),
                ] ),
            ] )
            ->recordActionsColumnLabel( '操作' );
    }

    /**
     * 创建发送信息操作。
     * 将信息作为 Filament 数据库通知发送给目标管理员。
     * @return Action 发送信息操作
     */
    private static function sendMessageAction(): Action {
        return Action::make( 'sendMessage' )
            ->label( __( 'filament.actions.send_message' ) )
            ->icon( Heroicon::OutlinedPaperAirplane )
            ->color( 'primary' )
            ->authorize( function ( AdminUser $record ): bool {
                $adminUser = auth( 'admin' )->user();
                return $adminUser instanceof AdminUser &&
                    $adminUser->status &&
                    ( $adminUser->is( $record ) || $record->level < $adminUser->level );
            } )
            ->modalHeading( fn ( AdminUser $record ): string => __( 'filament.notifications.send.heading', [
                'name' => $record->name,
            ] ) )
            ->modalSubmitActionLabel( __( 'filament.actions.send' ) )
            ->modalWidth( '4xl' )
            ->schema( DatabaseNotificationConfig::schema() )
            ->action( function ( array $data, AdminUser $record ): void {
                try {
                    DB::transaction( function () use ( $data, $record ): void {
                        DatabaseNotificationConfig::make( $data )
                            ->sendToDatabase( $record );
                    } );
                }catch ( Throwable $exception ) {
                    report( $exception );
                    Notification::make()
                        ->title( __( 'filament.notifications.send.failed' ) )
                        ->danger()
                        ->send();
                    return;
                }
                Notification::make()
                    ->title( __( 'filament.notifications.send.success' ) )
                    ->success()
                    ->send();
            } );
    }
}
