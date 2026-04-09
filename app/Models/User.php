<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'google_id',
        'password',
        'target_min',
        'target_max',
        'birth_date',
        'gender',
        'diabetes_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'target_min' => 'integer',
            'target_max' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function readings()
    {
        return $this->hasMany(Reading::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * Logic
     */
    public function calculateStreak()
    {
        $dates = $this->readings()
            ->selectRaw('DATE(timestamp) as reading_date')
            ->groupBy('reading_date')
            ->orderBy('reading_date', 'desc')
            ->pluck('reading_date')
            ->map(fn($date) => \Illuminate\Support\Carbon::parse($date));

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $lastDate = $dates->first()->startOfDay();

        if ($lastDate < $yesterday) {
            return 0;
        }

        $streak = 0;
        $currentDate = $lastDate;

        foreach ($dates as $date) {
            $date = $date->startOfDay();
            if ($date->equalTo($currentDate)) {
                $streak++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
