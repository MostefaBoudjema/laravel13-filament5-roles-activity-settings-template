<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value',  'editable', 'type'];

    protected $casts = [
        'editable' => 'boolean',
    ];
protected static function booted(): void
{
    static::saved(fn (Setting $setting) => cache()->forget("setting.{$setting->key}"));
}
}
