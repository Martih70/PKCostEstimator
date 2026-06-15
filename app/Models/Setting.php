<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Get a setting value by key, or $default if it doesn't exist.
     */
    public static function get(string $key, $default = null)
    {
        $value = static::where('key', $key)->value('value');

        return $value !== null ? $value : $default;
    }

    /**
     * Create or update a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
