<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

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
                    ->label( 'ID' )
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                ViewColumn::make( 'name' )
                    ->label( '用户名' )
                    ->view( 'Filament.AdminControl.AdminUsers.admin-user-name' )
                    ->searchable(),
                TextColumn::make( 'email' )
                    ->label( '邮箱' )
                    ->searchable(),
                IconColumn::make( 'status' )
                    ->label( '状态' )
                    ->boolean(),
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
