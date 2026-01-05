<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_key',
        'config_group',
        'config_value',
        'config_type',
        'description',
    ];

    /**
     * Lấy giá trị cấu hình theo key
     */
    public static function getValue($key, $default = null)
    {
        $config = self::where('config_key', $key)->first();
        if (!$config) {
            return $default;
        }

        return match($config->config_type) {
            'json' => json_decode($config->config_value, true) ?? $default,
            'integer' => (int) $config->config_value,
            'boolean' => filter_var($config->config_value, FILTER_VALIDATE_BOOLEAN),
            default => $config->config_value ?? $default,
        };
    }

    /**
     * Lưu giá trị cấu hình
     */
    public static function setValue($key, $value, $group = 'general', $type = 'string', $description = null)
    {
        $config = self::updateOrCreate(
            ['config_key' => $key],
            [
                'config_group' => $group,
                'config_value' => is_array($value) ? json_encode($value) : $value,
                'config_type' => is_array($value) ? 'json' : $type,
                'description' => $description,
            ]
        );
        return $config;
    }

    /**
     * Lấy tất cả cấu hình theo group
     */
    public static function getByGroup($group)
    {
        return self::where('config_group', $group)->get()->pluck('config_value', 'config_key')->toArray();
    }
}
