<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserPhoneTest extends TestCase {
    /**
     * 测试电话号码存储格式。
     * 数据库存储值不应包含加号。
     */
    public function test_phone_storage_format_does_not_contain_plus_sign(): void {
        $this->assertSame( '86 13800138000', User::formatPhoneForStorage( '+86', '13800138000' ) );
        $this->assertNull( User::formatPhoneForStorage( '86', null ) );
    }

    /**
     * 测试电话号码拆分。
     */
    public function test_phone_can_be_split_for_form_fields(): void {
        $this->assertSame( [
            'area_code' => '86',
            'number' => '13800138000',
        ], User::splitPhone( '86 13800138000' ) );
    }

    /**
     * 测试电话号码显示格式。
     */
    public function test_phone_display_format_contains_plus_sign(): void {
        $this->assertSame( '+86 13800138000', User::formatPhoneForDisplay( '86 13800138000' ) );
        $this->assertNull( User::formatPhoneForDisplay( null ) );
    }
}
