<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BmiLog extends Model
{
    protected $fillable = [
        'user_id',
        'weight_kg',
        'height_cm',
        'bmi_value',
        'category',
        'advice',
    ];

    protected $casts = [
        'weight_kg' => 'float',
        'height_cm' => 'float',
        'bmi_value' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
