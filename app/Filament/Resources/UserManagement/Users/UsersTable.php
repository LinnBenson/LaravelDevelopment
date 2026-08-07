<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Filament\Config\DatabaseNotificationConfig;
use App\Models\AdminUser;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Filament\Config\SendNotification;

/**
 * UsersTable
 * 前台用户列表。
 * @package App\Filament\Resources\UserManagement\Users
 */
class UsersTable {
    /**
     * 配置表格。
     * 配置用户列表字段、筛选和操作。
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
                SelectColumn::make( 'agent' )
                    ->label( '代理' )
                    ->visible( fn (): bool =>
                        (int) auth( 'admin' )->user()?->level > (int) config( 'filament.agent', 100 )
                    )
                    ->options( fn (): array => [0 => '0 · System'] + AdminUser::query()
                        ->where( 'level', '<=', (int) config( 'filament.agent', 100 ) )
                        ->orderBy( 'name' )
                        ->get()
                        ->mapWithKeys( fn ( AdminUser $adminUser ): array => [
                            $adminUser->getKey() => "{$adminUser->id} · {$adminUser->name}",
                        ] )
                        ->all() )
                    ->disabled( function ( User $record ): bool {
                        $adminUser = auth( 'admin' )->user();
                        return !$adminUser instanceof AdminUser ||
                            $adminUser->level <= (int) config( 'filament.agent', 100 ) ||
                            Gate::forUser( $adminUser )->denies( 'update', $record );
                    } )
                    ->updateStateUsing( function ( int|string|null $state, User $record ): int {
                        $oldAgent = AdminUser::query()
                            ->where( 'level', '<=', (int) config( 'filament.agent', 100 ) )
                            ->find( $record->agent );
                        $adminUser = auth( 'admin' )->user();
                        abort_unless(
                            $adminUser instanceof AdminUser &&
                            $adminUser->level > (int) config( 'filament.agent', 100 ) &&
                            Gate::forUser( $adminUser )->allows( 'update', $record ),
                            403,
                        );
                        $agent = (int) $state;
                        $agentAdmin = $agent === 0 ? null : AdminUser::query()
                            ->where( 'level', '<=', (int) config( 'filament.agent', 100 ) )
                            ->find( $agent );
                        if ( $agent !== 0 && !$agentAdmin instanceof AdminUser ) {
                            throw ValidationException::withMessages( ['agent' => '选择的代理不存在。'] );
                        }
                        $record->agent = $agent;
                        $record->save();
                        $agentName = $agent === 0
                            ? '0 · System'
                            : "{$agent} · {$agentAdmin->name}";
                        if ( $oldAgent instanceof AdminUser ) {
                            DB::transaction( function () use ( $oldAgent, $record ): void {
                                SendNotification::make()
                                    ->title( '新用户已移除' )
                                    ->body( "用户 {$record->id} 已被从您的名下移除，现在您已无法管理此用户。" )
                                    ->status( 'success' )
                                    ->persistent()
                                    ->sendToDatabase( $oldAgent );
                            } );
                        }
                        if ( $agentAdmin instanceof AdminUser ) {
                            DB::transaction( function () use ( $agentAdmin, $record ): void {
                                SendNotification::make()
                                    ->title( '新用户已添加' )
                                    ->body( "用户 {$record->id} 已被分配至您的名下，现在您可以管理此用户。" )
                                    ->status( 'success' )
                                    ->persistent()
                                    ->sendToDatabase( $agentAdmin );
                            } );
                        }
                        Notification::make()
                            ->title( '代理修改成功' )
                            ->body( "用户 {$record->id} 的代理已修改为 {$agentName}。" )
                            ->success()
                            ->send();
                        return $agent;
                    } )
                    ->searchableOptions()
                    ->preloadOptions()
                    ->native( false ),
                TextColumn::make( 'nickname' )
                    ->label( '昵称' )
                    ->searchable()
                    ->placeholder( '-' ),
                ViewColumn::make( 'name' )
                    ->label( '用户名' )
                    ->view( 'Filament::UserManagement.Users.user-name' )
                    ->searchable(),
                TextColumn::make( 'email' )
                    ->label( '邮箱' )
                    ->searchable()
                    ->placeholder( '-' ),
                TextColumn::make( 'phone' )
                    ->label( '电话' )
                    ->formatStateUsing( fn ( ?string $state ): ?string => User::formatPhoneForDisplay( $state ) )
                    ->searchable()
                    ->placeholder( '-' ),
                ToggleColumn::make( 'status' )
                    ->label( '状态' )
                    ->onColor( 'success' )
                    ->offColor( 'danger' )
                    ->onIcon( 'heroicon-m-check' )
                    ->offIcon( 'heroicon-m-x-mark' )
                    ->disabled( function ( User $record ): bool {
                        $adminUser = auth( 'admin' )->user();
                        return $adminUser === null || Gate::forUser( $adminUser )->denies( 'update', $record );
                    } )
                    ->afterStateUpdated( function ( bool $state, User $record ): void {
                        $name = filled( $record->nickname ) ? $record->nickname : ( $record->name ?: "UID {$record->id}" );
                        $status = $state ? '启用' : '禁用';
                        Notification::make()
                            ->title( '用户状态修改成功' )
                            ->body( "用户 {$name} 已{$status}。" )
                            ->success()
                            ->send();
                    } ),
                TextColumn::make( 'level' )
                    ->label( '级别' )
                    ->formatStateUsing( fn ( int $state ): string => User::getLevel( $state ) )
                    ->badge()
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
                SelectFilter::make( 'agent' )
                    ->label( '代理' )
                    ->visible( fn (): bool =>
                        (int) auth( 'admin' )->user()?->level > (int) config( 'filament.agent', 100 )
                    )
                    ->options( fn (): array => [0 => '0 · System'] + AdminUser::query()
                        ->where( 'level', '<=', (int) config( 'filament.agent', 100 ) )
                        ->orderBy( 'name' )
                        ->get()
                        ->mapWithKeys( fn ( AdminUser $adminUser ): array => [
                            $adminUser->getKey() => "{$adminUser->id} · {$adminUser->name}",
                        ] )
                        ->all() )
                    ->searchable()
                    ->preload()
                    ->native( false ),
                TernaryFilter::make( 'status' )
                    ->label( '状态' ),
                SelectFilter::make( 'level' )
                    ->label( '级别' )
                    ->options( collect( User::getLevel() )
                        ->mapWithKeys( fn ( string $name, int $level ): array => [$level => $name] )
                        ->all() )
                    ->native( false ),
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
            ->recordActionsColumnLabel( '操作' )
            ->toolbarActions( [
                BulkActionGroup::make( [
                    DeleteBulkAction::make()
                        ->label( '删除所选' )
                        ->authorizeIndividualRecords( 'delete' ),
                ] ),
            ] );
    }

    /**
     * 创建发送消息操作。
     * 将消息作为 Filament 数据库通知发送给目标用户。
     * @return Action 发送消息操作
     */
    private static function sendMessageAction(): Action {
        return Action::make( 'sendMessage' )
            ->label( __( 'filament.actions.send_user_message' ) )
            ->icon( Heroicon::OutlinedPaperAirplane )
            ->color( 'primary' )
            ->authorize( function ( User $record ): bool {
                $adminUser = auth( 'admin' )->user();
                return $adminUser instanceof AdminUser &&
                    $adminUser->status &&
                    (
                        $adminUser->level > (int) config( 'filament.agent', 100 ) ||
                        $record->agent === $adminUser->getKey()
                    );
            } )
            ->modalHeading( fn ( User $record ): string => __( 'filament.notifications.send.heading', [
                'name' => filled( $record->nickname ) ? $record->nickname : ( $record->name ?: "UID {$record->id}" ),
            ] ) )
            ->modalSubmitActionLabel( __( 'filament.actions.send' ) )
            ->modalWidth( '4xl' )
            ->schema( DatabaseNotificationConfig::schema() )
            ->action( function ( array $data, User $record ): void {
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
