<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserLevelTest extends TestCase {
    /**
     * 测试获取完整等级列表。
     */
    public function test_get_level_returns_all_levels_without_an_argument(): void {
        $this->assertSame( User::LEVELS, User::getLevel() );
    }

    /**
     * 测试等级按最接近上限匹配。
     */
    public function test_get_level_returns_name_by_upper_limit(): void {
        $this->assertSame( 'Visitor', User::getLevel( 0 ) );
        $this->assertSame( 'System User', User::getLevel( 1 ) );
        $this->assertSame( 'System User', User::getLevel( '10' ) );
        $this->assertSame( 'User', User::getLevel( 11 ) );
        $this->assertSame( 'Verified User', User::getLevel( 100 ) );
        $this->assertSame( 'Member', User::getLevel( 101 ) );
    }

    /**
     * 测试非法或超出范围的等级。
     */
    public function test_get_level_returns_unknown_for_invalid_levels(): void {
        $this->assertSame( 'Unknown', User::getLevel( '' ) );
        $this->assertSame( 'Unknown', User::getLevel( 'invalid' ) );
        $this->assertSame( 'Unknown', User::getLevel( -1 ) );
        $this->assertSame( 'Unknown', User::getLevel( 1001 ) );
    }
}
