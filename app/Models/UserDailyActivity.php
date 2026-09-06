<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDailyActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_date',
        'activity_type',
        'points_earned',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
