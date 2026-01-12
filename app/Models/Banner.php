<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'link_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

