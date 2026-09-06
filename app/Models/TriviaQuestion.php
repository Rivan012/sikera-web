<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriviaQuestion extends Model
{
    protected $fillable = [
        'question',
        'choices',
        'answer_key',
        'scientific_explanation',
    ];

    protected $casts = [
        'choices' => 'array',
    ];
}
