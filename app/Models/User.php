<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User
 * 用户模型。
 * @package App\Models
 */
class User extends Authenticatable {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * 用户字段备注。
     *
     * @var array<string, string>
     */
    public const FIELD_COMMENTS = [
        'id' => '用户ID',
        'agent' => '上级代理管理员ID',
        'name' => '用户名',
        'email' => '邮箱',
        'phone' => '电话',
        'status' => '状态：1启用，0禁用',
        'level' => '级别',
        'nickname' => '昵称',
        'password' => '密码',
        'avatar' => '头像',
        'remember_token' => '记住登录',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
    ];

    /**
     * 用户等级上限与名称。
     *
     * @var array<int, string>
     */
    public const LEVELS = [
        0 => 'Visitor',
        10 => 'System User',
        50 => 'User',
        100 => 'Verified User',
        1000 => 'Member',
    ];

    /**
     * 可以批量赋值的属性。
     *
     * @var list<string>
     */
    protected $fillable = [
        'agent',
        'name',
        'email',
        'phone',
        'status',
        'level',
        'nickname',
        'password',
        'avatar',
    ];

    /**
     * 序列化时需要隐藏的属性。
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 获取需要类型转换的属性。
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'agent' => 'integer',
            'status' => 'boolean',
            'level' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * 获取上级代理管理员。
     * 根据 agent 字段关联后台管理员。
     * @return BelongsTo<AdminUser, $this> 上级代理管理员关系
     */
    public function agentAdmin(): BelongsTo {
        return $this->belongsTo( AdminUser::class, 'agent', 'id' );
    }

    /**
     * 获取字段备注列表。
     * 获取用户表所有字段对应的中文备注。
     * @return array<string, string> 字段备注列表
     */
    public static function fields(): array {
        return self::FIELD_COMMENTS;
    }

    /**
     * 获取字段备注。
     * 根据字段名获取用户表字段对应的中文备注。
     * @param string $field 字段名
     * @return string 字段备注
     */
    public static function field( string $field ): string {
        return self::FIELD_COMMENTS[$field] ?? '';
    }

    /**
     * 获取用户等级。
     * 不传等级时返回全部配置，传入等级时按最接近的上限返回名称。
     * @param int|string|null $level 用户等级
     * @return array<int, string>|string 等级列表或等级名称
     */
    public static function getLevel( int|string|null $level = null ): array|string {
        if ( $level === null ) { return self::LEVELS; }
        $normalizedLevel = is_string( $level ) ? trim( $level ) : $level;
        if ( $normalizedLevel === '' || ! filter_var( $normalizedLevel, FILTER_VALIDATE_INT ) && $normalizedLevel !== 0 && $normalizedLevel !== '0' ) {
            return 'Unknown';
        }
        $normalizedLevel = (int) $normalizedLevel;
        if ( $normalizedLevel < 0 ) { return 'Unknown'; }
        foreach ( self::LEVELS as $maximumLevel => $name ) {
            if ( $normalizedLevel <= $maximumLevel ) { return $name; }
        }
        return 'Unknown';
    }

    /**
     * 组合电话号码。
     * 将国际区号和本地号码组合为不带加号的存储格式。
     * @param string|null $areaCode 国际区号
     * @param string|null $number 本地号码
     * @return string|null 存储格式电话号码
     */
    public static function formatPhoneForStorage( ?string $areaCode, ?string $number ): ?string {
        $areaCode = preg_replace( '/\D+/', '', $areaCode ?? '' );
        $number = preg_replace( '/\D+/', '', $number ?? '' );
        if ( $number === '' ) { return null; }
        if ( $areaCode === '' ) { $areaCode = (string) config( 'areacodes.default', '1' ); }
        return "{$areaCode} {$number}";
    }

    /**
     * 拆分电话号码。
     * 将已存储的电话号码拆分为国际区号和本地号码。
     * @param string|null $phone 已存储的电话号码
     * @return array{area_code: string, number: string} 拆分后的电话号码
     */
    public static function splitPhone( ?string $phone ): array {
        $defaultAreaCode = (string) config( 'areacodes.default', '1' );
        $phone = trim( $phone ?? '' );
        if ( $phone === '' ) { return ['area_code' => $defaultAreaCode, 'number' => '']; }
        if ( preg_match( '/^\+?(\d{1,3})\s+(\d+)$/', $phone, $matches ) === 1 ) {
            return ['area_code' => $matches[1], 'number' => $matches[2]];
        }
        return [
            'area_code' => $defaultAreaCode,
            'number' => preg_replace( '/\D+/', '', $phone ) ?? '',
        ];
    }

    /**
     * 格式化电话号码显示。
     * 在已存储的电话号码前增加加号。
     * @param string|null $phone 已存储的电话号码
     * @return string|null 显示格式电话号码
     */
    public static function formatPhoneForDisplay( ?string $phone ): ?string {
        $parts = self::splitPhone( $phone );
        if ( $parts['number'] === '' ) { return null; }
        return "+{$parts['area_code']} {$parts['number']}";
    }
}
