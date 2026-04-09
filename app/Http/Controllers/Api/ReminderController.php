<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Display a listing of the reminders.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->reminders);
    }

    /**
     * Store a newly created reminder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'time' => 'required|string', // HH:mm
            'label' => 'required|string',
            'days' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $reminder = $request->user()->reminders()->create($request->all());

        return response()->json($reminder, 201);
    }

    /**
     * Display the specified reminder.
     */
    public function show(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($reminder);
    }

    /**
     * Update the specified reminder.
     */
    public function update(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'time' => 'sometimes|string',
            'label' => 'sometimes|string',
            'days' => 'sometimes|array',
            'is_active' => 'boolean',
        ]);

        $reminder->update($request->all());

        return response()->json($reminder);
    }

    /**
     * Remove the specified reminder.
     */
    public function destroy(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reminder->delete();

        return response()->json(['message' => 'تم حذف التذكير بنجاح']);
    }
}
