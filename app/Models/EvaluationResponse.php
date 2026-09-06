<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationResponse extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'module_number',
        'raw_answers',
        'scores_per_module',
        'total_score',
        'n_gain_score',
        'submitted_at',
    ];

    protected $casts = [
        'raw_answers' => 'array',
        'scores_per_module' => 'array',
        'submitted_at' => 'datetime',
        'n_gain_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
