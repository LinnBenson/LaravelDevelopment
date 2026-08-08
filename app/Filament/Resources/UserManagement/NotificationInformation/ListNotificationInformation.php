<?php

namespace App\Filament\Resources\UserManagement\NotificationInformation;

use Filament\Resources\Pages\ListRecords;

/**
 * ListNotificationInformation
 * 后台通知信息列表页面。
 * @package App\Filament\Resources\UserManagement\NotificationInformation
 */
class ListNotificationInformation extends ListRecords {
    protected static string $resource = NotificationInformationResource::class;
}
