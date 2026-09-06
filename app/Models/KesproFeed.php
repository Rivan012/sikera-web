<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KesproFeed extends Model
{
    protected $fillable = [
        'title',
        'category',
        'image_path',
        'caption',
        'share_text',
        'download_count',
        'share_count',
    ];
}
