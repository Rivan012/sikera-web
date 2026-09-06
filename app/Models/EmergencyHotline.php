<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyHotline extends Model
{
    protected $fillable = [
        'name',
        'category',
        'phone_number',
        'whatsapp_number',
        'address',
        'operating_hours',
        'description',
    ];
}
