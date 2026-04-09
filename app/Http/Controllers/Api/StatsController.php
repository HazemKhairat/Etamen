<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Get summary statistics for the user.
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $readings = $user->readings();

        if ($readings->count() === 0) {
            return response()->json([
                'avg' => 0,
                'min' => 0,
                'max' => 0,
                'status_counts' => [
                    'Low' => 0,
                    'Normal' => 0,
                    'Warning' => 0,
                    'High' => 0,
                ],
                'improvement' => 0,
            ]);
        }

        $avg = round($readings->avg('value'), 1);
        $min = $readings->min('value');
        $max = $readings->max('value');

        // Status percentages
        $totalCount = $readings->count();
        $statusCounts = $readings->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are present
        $statuses = ['Low', 'Normal', 'Warning', 'High'];
        $formattedStatusCounts = [];
        foreach ($statuses as $status) {
            $count = $statusCounts[$status] ?? 0;
            $formattedStatusCounts[$status] = [
                'count' => $count,
                'percentage' => $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0
            ];
        }

        // Improvement (vs last 7 days)
        $thisWeekAvg = $user->readings()
            ->where('timestamp', '>=', Carbon::now()->subDays(7))
            ->avg('value');
        
        $lastWeekAvg = $user->readings()
            ->where('timestamp', '<', Carbon::now()->subDays(7))
            ->where('timestamp', '>=', Carbon::now()->subDays(14))
            ->avg('value');

        $improvement = 0;
        if ($lastWeekAvg > 0) {
            $improvement = round((($lastWeekAvg - $thisWeekAvg) / $lastWeekAvg) * 100, 1);
        }

        return response()->json([
            'avg' => $avg,
            'min' => $min,
            'max' => $max,
            'status_stats' => $formattedStatusCounts,
            'improvement' => $improvement,
        ]);
    }

    /**
     * Get chart data for line charts.
     */
    public function chart(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', 'week'); // week, month

        $days = ($period === 'month') ? 30 : 7;

        $chartData = $user->readings()
            ->where('timestamp', '>=', Carbon::now()->subDays($days))
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('AVG(value) as avg_value')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($chartData);
    }
}
