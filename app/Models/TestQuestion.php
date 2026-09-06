<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $fillable = [
        'module_target',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
