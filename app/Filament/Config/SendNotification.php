<?php

namespace App\Filament\Config;

use Filament\Notifications\Notification;

/**
 * SendNotification
 * 保留发送表单设置的数据库通知持续时间。
 * @package App\Filament\Config
 */
class SendNotification extends Notification {
    /**
     * 获取数据库通知数据。
     * Filament 默认强制数据库通知持续显示，此处保留管理员设置的持续时间。
     * @return array<string, mixed> 数据库通知数据
     */
    public function getDatabaseMessage(): array {
        $data = $this->toArray();
        $data['format'] = 'filament';
        unset( $data['id'] );
        return $data;
    }
}
