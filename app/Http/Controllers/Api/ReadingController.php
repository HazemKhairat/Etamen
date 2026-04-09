<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    /**
     * Display a listing of the readings.
     */
    public function index(Request $request)
    {
        $query = $request->user()->readings()->orderBy('timestamp', 'desc');

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('timestamp', $request->date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $readings = $query->get();

        // Group by date for the "Readings Section" in the app
        $grouped = $readings->groupBy(function ($reading) {
            return $reading->timestamp->format('Y-m-d');
        })->map(function ($dayReadings, $date) {
            return [
                'date' => $date,
                'readings' => $dayReadings
            ];
        })->values();

        return response()->json($grouped);
    }

    /**
     * Store a newly created reading.
     */
    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|integer',
            'timestamp' => 'required|date',
            'context' => 'required|string', // قبل الاكل، بعد الاكل، صيام، قبل النوم
            'notes' => 'nullable|string',
        ]);

        $reading = $request->user()->readings()->create([
            'value' => $request->value,
            'timestamp' => $request->timestamp,
            'context' => $request->context,
            'notes' => $request->notes,
        ]);

        return response()->json($reading, 201);
    }

    /**
     * Display the specified reading.
     */
    public function show(Request $request, Reading $reading)
    {
        if ($reading->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($reading);
    }

    /**
     * Update the specified reading.
     */
    public function update(Request $request, Reading $reading)
    {
        if ($reading->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'value' => 'sometimes|integer',
            'timestamp' => 'sometimes|date',
            'context' => 'sometimes|string',
            'notes' => 'nullable|string',
        ]);

        $reading->update($request->all());

        return response()->json($reading);
    }

    /**
     * Remove the specified reading.
     */
    public function destroy(Request $request, Reading $reading)
    {
        if ($reading->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reading->delete();

        return response()->json(['message' => 'تم حذف القراءة بنجاح']);
    }
}
