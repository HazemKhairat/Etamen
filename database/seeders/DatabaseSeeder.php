<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'full_name' => 'محمد علي',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'target_min' => 70,
            'target_max' => 140,
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'diabetes_type' => 'type1',
        ]);

        // Creating readings for the last 10 days (4 readings per day)
        $contexts = ['قبل الاكل', 'بعد الاكل', 'صيام', 'قبل النوم'];
        
        for ($i = 0; $i < 40; $i++) {
            $user->readings()->create([
                'value' => rand(60, 220), // Mix of low, normal, warning, high
                'timestamp' => Carbon::now()->subHours($i * 6),
                'context' => $contexts[array_rand($contexts)],
                'notes' => 'قراءة تجريبية ' . ($i + 1),
            ]);
        }

        // Creating reminders
        $user->reminders()->createMany([
            [
                'time' => '08:00',
                'label' => 'قراءة الصباح',
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'is_active' => true,
            ],
            [
                'time' => '14:00',
                'label' => 'بعد الغداء',
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'is_active' => false,
            ],
            [
                'time' => '22:00',
                'label' => 'قراءة قبل النوم',
                'days' => ['Monday', 'Wednesday', 'Friday'],
                'is_active' => true,
            ],
        ]);
    }
}
