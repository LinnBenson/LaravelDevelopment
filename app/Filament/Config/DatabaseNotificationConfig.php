<?php

namespace App\Filament\Config;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

/**
 * DatabaseNotificationConfig
 * 提供数据库通知发送表单及通知对象构建逻辑。
 * @package App\Filament\Config
 */
class DatabaseNotificationConfig {
    /**
     * 获取发送通知表单。
     * @return array<int, mixed> 表单组件
     */
    public static function schema(): array {
        return [
            Section::make( '通知内容' )
                ->schema( [
                    TextInput::make( 'title' )
                        ->label( __( 'filament.notifications.fields.title' ) )
                        ->required()
                        ->maxLength( 255 ),
                    Textarea::make( 'body' )
                        ->label( __( 'filament.notifications.fields.body' ) )
                        ->required()
                        ->rows( 5 )
                        ->maxLength( 2000 ),
                ] ),
            Section::make( '显示设置' )
                ->collapsed()
                ->schema( [
                    Grid::make( ['default' => 1, 'md' => 3] )
                        ->schema( [
                            Select::make( 'status' )
                                ->label( '状态' )
                                ->options( self::statusOptions() )
                                ->default( 'info' )
                                ->native( false ),
                            Select::make( 'color' )
                                ->label( '整体颜色' )
                                ->options( self::colorOptions() )
                                ->placeholder( '默认' )
                                ->native( false ),
                            Select::make( 'iconColor' )
                                ->label( '图标颜色' )
                                ->options( self::colorOptions() )
                                ->placeholder( '跟随状态' )
                                ->native( false ),
                        ] ),
                    Grid::make( ['default' => 1, 'md' => 3] )
                        ->schema( [
                            TextInput::make( 'icon' )
                                ->label( '图标' )
                                ->placeholder( '留空时跟随状态' )
                                ->helperText( '例如 heroicon-o-bell' )
                                ->maxLength( 100 ),
                            Select::make( 'durationMode' )
                                ->label( '显示方式' )
                                ->options( [
                                    'persistent' => '持续显示',
                                    'timed' => '定时关闭',
                                ] )
                                ->default( 'persistent' )
                                ->required()
                                ->live()
                                ->native( false ),
                            TextInput::make( 'duration' )
                                ->label( '持续时间（毫秒）' )
                                ->numeric()
                                ->default( 6000 )
                                ->required( fn ( Get $get ): bool => $get( 'durationMode' ) === 'timed' )
                                ->visible( fn ( Get $get ): bool => $get( 'durationMode' ) === 'timed' )
                                ->minValue( 500 )
                                ->maxValue( 86400000 ),
                        ] ),
                ] )
                ->columns( 1 ),
            Section::make( '操作按钮' )
                ->collapsed()
                ->schema( [
                    Repeater::make( 'actions' )
                        ->label( '按钮' )
                        ->defaultItems( 0 )
                        ->addActionLabel( '添加按钮' )
                        ->itemLabel( fn ( array $state ): ?string => $state['label'] ?? null )
                        ->schema( [
                            Grid::make( ['default' => 1, 'md' => 2] )
                                ->schema( [
                                    TextInput::make( 'name' )
                                        ->label( '标识' )
                                        ->helperText( '仅允许字母、数字、短横线和下划线。' )
                                        ->required()
                                        ->regex( '/^[A-Za-z0-9_-]+$/' )
                                        ->maxLength( 64 ),
                                    TextInput::make( 'label' )
                                        ->label( '显示文字' )
                                        ->required()
                                        ->maxLength( 100 ),
                                    TextInput::make( 'url' )
                                        ->label( '链接' )
                                        ->placeholder( '/admin/example 或 https://example.com' )
                                        ->maxLength( 2048 ),
                                    TextInput::make( 'icon' )
                                        ->label( '图标' )
                                        ->placeholder( '例如 heroicon-o-eye' )
                                        ->maxLength( 100 ),
                                    Select::make( 'color' )
                                        ->label( '颜色' )
                                        ->options( self::colorOptions() )
                                        ->default( 'primary' )
                                        ->native( false ),
                                ] ),
                            Grid::make( ['default' => 1, 'md' => 3] )
                                ->schema( [
                                    Toggle::make( 'newTab' )
                                        ->label( '新窗口打开' ),
                                    Toggle::make( 'close' )
                                        ->label( '点击后关闭通知' ),
                                    Toggle::make( 'markAsRead' )
                                        ->label( '点击后标记已读' )
                                        ->default( true ),
                                ] ),
                        ] ),
                ] ),
        ];
    }

    /**
     * 根据表单数据构建通知。
     * @param array<string, mixed> $data 表单数据
     * @return SendNotification 通知对象
     */
    public static function make( array $data ): SendNotification {
        $notification = SendNotification::make()
            ->title( $data['title'] )
            ->body( filled( $data['body'] ?? null ) ? $data['body'] : null )
            ->status( filled( $data['status'] ?? null ) ? $data['status'] : null )
            ->color( filled( $data['color'] ?? null ) ? $data['color'] : null )
            ->icon( filled( $data['icon'] ?? null ) ? $data['icon'] : null )
            ->iconColor( filled( $data['iconColor'] ?? null ) ? $data['iconColor'] : null )
            ->duration( ( $data['durationMode'] ?? 'persistent' ) === 'timed'
                ? (int) $data['duration']
                : 'persistent' );

        $notification->actions( array_map(
            fn ( array $actionData ): Action => self::makeAction( $actionData ),
            $data['actions'] ?? [],
        ) );

        return $notification;
    }

    /**
     * 根据配置构建通知操作按钮。
     * @param array<string, mixed> $data 按钮配置
     * @return Action 通知操作
     */
    private static function makeAction( array $data ): Action {
        $action = Action::make( $data['name'] ?? Str::random( 8 ) )
            ->label( $data['label'] )
            ->color( $data['color'] ?? 'primary' )
            ->icon( filled( $data['icon'] ?? null ) ? $data['icon'] : null )
            ->close( (bool) ( $data['close'] ?? false ) )
            ->markAsRead( (bool) ( $data['markAsRead'] ?? true ) );

        if ( filled( $data['url'] ?? null ) ) {
            $action->url( $data['url'], (bool) ( $data['newTab'] ?? false ) );
        }

        return $action;
    }

    /**
     * 获取通知状态选项。
     * @return array<string, string> 状态选项
     */
    private static function statusOptions(): array {
        return [
            'success' => '成功',
            'info' => '信息',
            'warning' => '警告',
            'danger' => '危险',
        ];
    }

    /**
     * 获取 Filament 颜色选项。
     * @return array<string, string> 颜色选项
     */
    private static function colorOptions(): array {
        return [
            'primary' => '主题色',
            'success' => '成功色',
            'info' => '信息色',
            'warning' => '警告色',
            'danger' => '危险色',
            'gray' => '灰色',
        ];
    }
}
