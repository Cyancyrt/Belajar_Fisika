<?php
// filepath: app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'level',
        'total_xp',
        'streak_days',      //  (bukan streak_count)
        'last_activity_date', //  (bukan last_login_streak)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_activity_date' => 'date', // Sesuaikan dengan migration
    ];

    public function achievements()
    {
        return $this->hasMany(UserAchievement::class); // Ada table user_achievements
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class); // Ada table user_progress
    }

    public function attempts()
    {
        return $this->hasMany(SimulationAttempt::class); // Ada table simulation_attempts
    }
    // Method untuk update streak - sesuaikan dengan nama kolom di migration
    public function updateStreak()
    {
        $today = Carbon::today();
        $lastActivity = $this->last_activity_date ? Carbon::parse($this->last_activity_date) : null;

        if (!$lastActivity || $lastActivity->diffInDays($today) > 1) {
            $this->streak_days = 1;
        } elseif ($lastActivity->diffInDays($today) === 1) {
            $this->streak_days += 1;
        }

        $this->last_activity_date = $today;
        $this->save();
    }
}
