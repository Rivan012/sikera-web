<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodLog extends Model
{
    protected $fillable = [
        'user_id',
        'menarche_age',
        'start_date',
        'end_date',
        'cycle_length',
        'period_duration',
        'is_regular',
        'flow_level',
        'flow_color',
        'blood_consistency',
        'volume_category',
        'pbac_score',
        'pbac_details',
        'nrs_pain_score',
        'has_dysmenorrhea',
        'walidd_working_ability',
        'walidd_locations',
        'walidd_location_score',
        'walidd_intensity_score',
        'walidd_pain_days',
        'walidd_pain_days_score',
        'walidd_total_score',
        'walidd_category',
        'walidd_interpretation',
        'symptoms',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'menarche_age' => 'integer',
        'is_regular' => 'boolean',
        'pbac_score' => 'integer',
        'pbac_details' => 'array',
        'has_dysmenorrhea' => 'boolean',
        'walidd_working_ability' => 'integer',
        'walidd_locations' => 'array',
        'walidd_location_score' => 'integer',
        'walidd_intensity_score' => 'integer',
        'walidd_pain_days' => 'integer',
        'walidd_pain_days_score' => 'integer',
        'walidd_total_score' => 'integer',
        'symptoms' => 'array',
        'nrs_pain_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
