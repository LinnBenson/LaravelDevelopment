<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\AdminUser;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

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
                TextColumn::make( 'agent' )
                    ->label( '代理' )
                    ->formatStateUsing( function ( int|string|null $state, User $record ): ?string {
                        if ( $state === null ) { return null; }
                        if ( (int) $state === 0 ) { return '0 · System'; }
                        return "{$state} · ".( $record->agentAdmin?->name ?? 'Unknown' );
                    } )
                    ->badge()
                    ->placeholder( '-' ),
                TextColumn::make( 'nickname' )
                    ->label( '昵称' )
                    ->searchable()
                    ->placeholder( '-' ),
                ViewColumn::make( 'name' )
                    ->label( '用户名' )
                    ->view( 'Filament.UserManagement.Users.user-name' )
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
                    ->options( fn (): array => [0 => '0 · System'] + AdminUser::query()
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
                EditAction::make()
                    ->label( '编辑' ),
            ] )
            ->recordActionsColumnLabel( '操作' )
            ->toolbarActions( [
                BulkActionGroup::make( [
                    DeleteBulkAction::make()
                        ->label( '删除所选' ),
                ] ),
            ] );
    }
}
