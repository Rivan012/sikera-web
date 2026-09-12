<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'initials',
        'email',
        'nim',
        'role',
        'usia',
        'menarche_age',
        'fakultas',
        'prodi',
        'gender',
        'agama',
        'pendidikan_terakhir',
        'is_biodata_filled',
        'pretest_completed',
        'posttest_completed',
        'points',
        'streak_days',
        'last_activity_date',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_biodata_filled' => 'boolean',
            'pretest_completed' => 'boolean',
            'posttest_completed' => 'boolean',
            'last_activity_date' => 'date',
        ];
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen_pa';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function evaluations()
    {
        return $this->hasMany(EvaluationResponse::class);
    }

    public function periodLogs()
    {
        return $this->hasMany(PeriodLog::class);
    }

    public function bmiLogs()
    {
        return $this->hasMany(BmiLog::class);
    }

    public function moduleProgresses()
    {
        return $this->hasMany(ModuleProgress::class);
    }

    public function diaries()
    {
        return $this->hasMany(SecretDiary::class);
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    public function dailyActivities()
    {
        return $this->hasMany(UserDailyActivity::class);
    }
}
