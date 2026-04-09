<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'value',
        'timestamp',
        'context',
        'status',
        'notes',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'value' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($reading) {
            if (!$reading->status) {
                $user = $reading->user ?: User::find($reading->user_id);
                if ($user) {
                    $val = $reading->value;
                    if ($val < $user->target_min) {
                        $reading->status = 'Low';
                    } elseif ($val <= $user->target_max) {
                        $reading->status = 'Normal';
                    } elseif ($val <= $user->target_max + 40) {
                        $reading->status = 'Warning';
                    } else {
                        $reading->status = 'High';
                    }
                }
            }
        });
    }
}
