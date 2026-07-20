<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CommonHelperTest extends TestCase
{
    /**
     * 测试 JSON 字符串判断
     * 仅 JSON 对象和 JSON 数组字符串应返回 true。
     */
    public function test_is_json_only_accepts_json_object_or_array_strings(): void
    {
        $this->assertTrue(is_json('{}'));
        $this->assertTrue(is_json('[]'));
        $this->assertTrue(is_json('{"name":"测试"}'));
        $this->assertTrue(is_json('[1,2,3]'));

        $this->assertFalse(is_json(''));
        $this->assertFalse(is_json('abc'));
        $this->assertFalse(is_json('"abc"'));
        $this->assertFalse(is_json('123'));
        $this->assertFalse(is_json('true'));
        $this->assertFalse(is_json('null'));
        $this->assertFalse(is_json('{name:"测试"}'));
    }

    /**
     * 测试对象公开方法判断
     * 仅对象上存在的 public 方法应返回 true。
     */
    public function test_is_public_only_accepts_public_object_methods(): void
    {
        $object = new CommonHelperTestObject();

        $this->assertTrue(isPublic($object, 'publicMethod'));

        $this->assertFalse(isPublic($object, 'protectedMethod'));
        $this->assertFalse(isPublic($object, 'privateMethod'));
        $this->assertFalse(isPublic($object, 'missingMethod'));
        $this->assertFalse(isPublic($object, ''));
    }

    /**
     * 测试 UUID 判断
     * 标准 UUID 格式应返回 true，其它格式应返回 false。
     */
    public function test_is_uuid_only_accepts_uuid_strings(): void
    {
        $this->assertTrue(is_uuid('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertTrue(is_uuid('550E8400-E29B-41D4-A716-446655440000'));

        $this->assertFalse(is_uuid(''));
        $this->assertFalse(is_uuid('550e8400e29b41d4a716446655440000'));
        $this->assertFalse(is_uuid('550e8400-e29b-41d4-a716'));
        $this->assertFalse(is_uuid('not-a-uuid'));
    }

    /**
     * 测试 UUID 生成
     * 生成结果应为合法 UUID 字符串。
     */
    public function test_uuid_returns_uuid_string(): void
    {
        $this->assertTrue(is_uuid(uuid()));
    }

    /**
     * 测试随机字符串生成
     * 根据类型生成指定长度的随机字符串。
     */
    public function test_random_string_returns_string_by_type(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9]{16}$/', random_string(16, 0));
        $this->assertMatchesRegularExpression('/^[a-zA-Z]{16}$/', random_string(16, 1));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]{16}$/', random_string(16, 2));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]{16}$/', random_string(16));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]{16}$/', random_string(16, 99));
        $this->assertSame('', random_string(0));
        $this->assertSame('', random_string(-1));
    }
}

class CommonHelperTestObject
{
    public function publicMethod(): void
    {
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }
}
