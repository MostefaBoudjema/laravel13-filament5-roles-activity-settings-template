<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value',  'editable', 'type'];

    protected $casts = [
        'editable' => 'boolean',
    ];

}
