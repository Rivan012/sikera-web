<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    protected $fillable = [
        'user_id',
        'anonymous_alias',
        'title',
        'topic',
        'content',
        'is_answered_by_counselor',
    ];

    protected $casts = [
        'is_answered_by_counselor' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class)->oldest();
    }
}
