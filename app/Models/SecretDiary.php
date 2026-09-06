<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretDiary extends Model
{
    protected $fillable = [
        'user_id',
        'mood',
        'encrypted_note',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
