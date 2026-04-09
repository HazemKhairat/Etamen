<?php

namespace Tests\Feature;

use App\Models\Reading;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingStatusTest extends TestCase
{
    /**
     * Test status calculation logic.
     */
    public function test_reading_status_calculation()
    {
        $user = User::factory()->create([
            'target_min' => 70,
            'target_max' => 140,
        ]);

        // Low
        $readingLow = Reading::create([
            'user_id' => $user->id,
            'value' => 60,
            'timestamp' => now(),
            'context' => 'صيام',
        ]);
        $this->assertEquals('Low', $readingLow->status);

        // Normal
        $readingNormal = Reading::create([
            'user_id' => $user->id,
            'value' => 100,
            'timestamp' => now(),
            'context' => 'صيام',
        ]);
        $this->assertEquals('Normal', $readingNormal->status);

        // Warning
        $readingWarning = Reading::create([
            'user_id' => $user->id,
            'value' => 160,
            'timestamp' => now(),
            'context' => 'صيام',
        ]);
        $this->assertEquals('Warning', $readingWarning->status);

        // High
        $readingHigh = Reading::create([
            'user_id' => $user->id,
            'value' => 200,
            'timestamp' => now(),
            'context' => 'صيام',
        ]);
        $this->assertEquals('High', $readingHigh->status);
    }
}
