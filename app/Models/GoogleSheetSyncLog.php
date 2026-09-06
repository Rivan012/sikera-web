<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleSheetSyncLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'student_identifier',
        'fakultas_prodi',
        'payload_data',
        'sync_status',
        'sheet_range',
        'synced_at',
    ];

    protected $casts = [
        'payload_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
