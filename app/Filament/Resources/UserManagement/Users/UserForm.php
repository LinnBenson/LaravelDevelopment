<?php

namespace App\Filament\Resources\UserManagement\Users;

use App\Models\User;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * UserForm
 * 前台用户表单。
 * @package App\Filament\Resources\UserManagement\Users
 */
class UserForm {
    /**
     * 配置表单。
     * 配置用户新增和编辑表单字段。
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
                        TextInput::make( 'nickname' )
                            ->label( '昵称' )
                            ->prefixIcon( Heroicon::OutlinedIdentification )
                            ->maxLength( 255 ),
                        TextInput::make( 'name' )
                            ->label( '用户名' )
                            ->prefixIcon( Heroicon::OutlinedUser )
                            ->maxLength( 255 )
                            ->unique( ignoreRecord: true ),
                        TextInput::make( 'email' )
                            ->label( '邮箱' )
                            ->prefixIcon( Heroicon::OutlinedEnvelope )
                            ->email()
                            ->maxLength( 255 )
                            ->unique( ignoreRecord: true ),
                        Grid::make( 3 )
                            ->schema( [
                                Select::make( 'phone_area_code' )
                                    ->label( '区号' )
                                    ->prefixIcon( Heroicon::OutlinedGlobeAlt )
                                    ->options( collect( array_keys( config( 'areacodes.data', [] ) ) )
                                        ->mapWithKeys( fn ( int|string $areaCode ): array => [
                                            (string) $areaCode => "+{$areaCode}",
                                        ] )
                                        ->all() )
                                    ->default( (string) config( 'areacodes.default', '1' ) )
                                    ->required()
                                    ->searchable()
                                    ->native( false )
                                    ->columnSpan( 1 ),
                                TextInput::make( 'phone' )
                                    ->label( '电话' )
                                    ->prefixIcon( Heroicon::OutlinedPhone )
                                    ->tel()
                                    ->regex( '/^[0-9]{4,15}$/' )
                                    ->maxLength( 15 )
                                    ->validationMessages( [
                                        'regex' => '电话号码只能包含 4 至 15 位数字。',
                                    ] )
                                    ->rules( [
                                        fn ( Get $get, ?User $record ): Closure => function ( string $attribute, ?string $value, Closure $fail ) use ( $get, $record ): void {
                                            $phone = User::formatPhoneForStorage( (string) $get( 'phone_area_code' ), $value );
                                            if ( $phone === null ) { return; }
                                            $query = User::query()->where( 'phone', $phone );
                                            if ( $record !== null ) { $query->whereKeyNot( $record->getKey() ); }
                                            if ( $query->exists() ) { $fail( '该电话号码已被使用。' ); }
                                        },
                                    ] )
                                    ->columnSpan( 2 ),
                            ] ),
                        Toggle::make( 'status' )
                            ->label( '状态' )
                            ->onIcon( Heroicon::OutlinedCheck )
                            ->offIcon( Heroicon::OutlinedXMark )
                            ->default( true )
                            ->required()
                            ->inline( false ),
                        Select::make( 'level' )
                            ->label( '级别' )
                            ->prefixIcon( Heroicon::OutlinedShieldCheck )
                            ->options( collect( User::getLevel() )
                                ->mapWithKeys( fn ( string $name, int $level ): array => [$level => "{$level} · {$name}"] )
                                ->all() )
                            ->required()
                            ->native( false )
                            ->default( 10 ),
                        TextInput::make( 'password' )
                            ->label( '密码' )
                            ->prefixIcon( Heroicon::OutlinedLockClosed )
                            ->password()
                            ->revealable()
                            ->required( fn ( string $operation ): bool => $operation === 'create' )
                            ->dehydrated( fn ( ?string $state ): bool => filled( $state ) )
                            ->minLength( 8 )
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
