<?php

namespace App\Filament\Resources\AdminControl\AdminUsers;

use App\Models\AdminUser;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * AdminUserForm
 * 管理员用户表单。
 * @package App\Filament\Resources\AdminControl\AdminUsers
 */
class AdminUserForm {
    /**
     * 配置表单。
     * 配置管理员用户新增和编辑表单字段。
     * @param Schema $schema 表单结构
     * @return Schema 表单结构
     */
    public static function configure( Schema $schema ): Schema {
        return $schema
            ->components( [
                Section::make( '基本信息' )
                    ->icon( Heroicon::OutlinedUserCircle )
                    ->columns( 1 )
                    ->schema( [
                        TextInput::make( 'name' )
                            ->label( '用户名' )
                            ->prefixIcon( Heroicon::OutlinedUser )
                            ->required()
                            ->maxLength( 255 )
                            ->unique( ignoreRecord: true ),
                        TextInput::make( 'email' )
                            ->label( '邮箱' )
                            ->prefixIcon( Heroicon::OutlinedEnvelope )
                            ->required()
                            ->email()
                            ->maxLength( 255 )
                            ->unique( ignoreRecord: true ),
                        Toggle::make( 'status' )
                            ->label( '状态' )
                            ->onIcon( Heroicon::OutlinedCheck )
                            ->offIcon( Heroicon::OutlinedXMark )
                            ->default( true )
                            ->required()
                            ->inline( false ),
                        TextInput::make( 'level' )
                            ->label( '级别' )
                            ->prefixIcon( Heroicon::OutlinedShieldCheck )
                            ->required()
                            ->numeric()
                            ->minValue( 0 )
                            ->maxValue( function ( ?AdminUser $record ): ?int {
                                if ( $record?->getKey() === Filament::auth()->id() ) { return null; }
                                return max( (int) Filament::auth()->user()?->level - 1, 0 );
                            } )
                            ->validationMessages( [
                                'max' => '级别必须低于当前管理员级别。',
                            ] )
                            ->disabled( fn ( ?AdminUser $record ): bool => $record?->getKey() === Filament::auth()->id() )
                            ->default( 1 ),
                        TextInput::make( 'password' )
                            ->label( '密码' )
                            ->prefixIcon( Heroicon::OutlinedLockClosed )
                            ->password()
                            ->revealable()
                            ->required( fn ( string $operation ): bool => $operation === 'create' )
                            ->dehydrated( fn ( ?string $state ): bool => filled( $state ) )
                            ->maxLength( 255 ),
                    ] ),
                Section::make( '头像设置' )
                    ->icon( Heroicon::OutlinedPhoto )
                    ->columns( 1 )
                    ->schema( [
                        FileUpload::make( 'avatar' )
                            ->label( '头像' )
                            ->hiddenLabel()
                            ->avatar()
                            ->image()
                            ->imageEditor()
                            ->disk( 'public' )
                            ->directory( 'avatars' )
                            ->visibility( 'public' )
                            ->maxSize( 2048 )
                            ->imagePreviewHeight( '160' )
                            ->acceptedFileTypes( ['image/jpeg', 'image/png', 'image/webp'] )
                            ->alignCenter(),
                    ] ),
            ] );
    }
}
