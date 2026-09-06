<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleTopic extends Model
{
    protected $fillable = [
        'educational_module_id',
        'topic_code',
        'title',
        'content_html',
        'youtube_video_id',
        'infographic_url',
        'order_index',
    ];

    public function module()
    {
        return $this->belongsTo(EducationalModule::class, 'educational_module_id');
    }

    public function progresses()
    {
        return $this->hasMany(ModuleProgress::class);
    }
}
