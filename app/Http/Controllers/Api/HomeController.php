<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get home screen data summary.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $lastReading = $user->readings()->orderBy('timestamp', 'desc')->first();
        $streak = $user->calculateStreak();

        return response()->json([
            'full_name' => $user->full_name,
            'last_reading' => $lastReading,
            'streak' => $streak,
            'target_range' => [
                'min' => $user->target_min,
                'max' => $user->target_max,
            ],
            'diabetes_type' => $user->diabetes_type,
        ]);
    }
}
