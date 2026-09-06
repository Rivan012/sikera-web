<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleProgress extends Model
{
    protected $fillable = [
        'user_id',
        'module_topic_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(ModuleTopic::class, 'module_topic_id');
    }
}
