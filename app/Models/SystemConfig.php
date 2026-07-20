<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SystemConfig
 * 系统配置模型。
 * @package App\Models
 */
class SystemConfig extends Model {
    use HasFactory;

    protected $table = 'system_config';

    /**
     * 配置类别。
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'app' => '应用配置',
        'system' => '系统配置',
        'other' => '其它配置',
    ];

    /**
     * 配置类型。
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'text' => '文本',
        'textarea' => '长文本',
        'boolean' => '布尔',
        'url' => '链接',
        'image' => '图片',
        'file' => '文件',
        'number' => '数字',
        'decimal' => '小数',
        'json' => 'JSON',
    ];

    /**
     * 可以批量赋值的属性。
     *
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'type',
        'name',
        'key',
        'value',
        'description',
        'index',
    ];

    /**
     * 获取需要类型转换的属性。
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'index' => 'integer',
        ];
    }

    /**
     * 获取类别名称。
     * 根据类别值返回对应中文名称。
     * @return string 类别名称
     */
    public function getCategoryLabelAttribute(): string {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * 获取类型名称。
     * 根据类型值返回对应中文名称。
     * @return string 类型名称
     */
    public function getTypeLabelAttribute(): string {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
