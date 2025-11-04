<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Obtener el valor de una configuración
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Obtener el valor como array (para JSON)
     */
    public static function getArrayValue($key, $default = [])
    {
        $value = static::getValue($key);
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : $default;
        }
        
        return is_array($value) ? $value : $default;
    }

    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function setArrayValue($key, array $value)
    {
        return static::setValue($key, json_encode($value));
    }

    public static function getBlockedExtensions()
    {
        return static::getArrayValue('blocked_extensions', []);
    }

    public static function updateBlockedExtensions(array $extensions)
    {
        return static::setArrayValue('blocked_extensions', $extensions);
    }
}