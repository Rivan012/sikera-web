<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MythFactCard extends Model
{
    protected $fillable = [
        'statement',
        'is_fact',
        'scientific_fact',
        'category',
    ];

    protected $casts = [
        'is_fact' => 'boolean',
    ];
}
