<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodLog extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'cycle_length',
        'period_duration',
        'flow_level',
        'flow_color',
        'nrs_pain_score',
        'symptoms',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'symptoms' => 'array',
        'nrs_pain_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
