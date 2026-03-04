<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleType;

class CalendarController extends Controller
{
    /**
     * Display the calendar and its events.
     */
    public function index(Request $request)
    {
        // Get the requested month/year or default to current
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Fetch events for the month
        $events = CalendarEvent::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();
            
        // Group events by date format 'Y-m-d' for easier lookups
        $groupedEvents = $events->groupBy(function($event) {
            return $event->date->format('Y-m-d');
        });

        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        return view('calendar.index', compact('groupedEvents', 'currentDate', 'month', 'year'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        // Check authorization (SUPER_ADMIN, DME_HEAD, HA_HEAD)
        if (!in_array(Auth::user()->role, [RoleType::SUPER_ADMIN, RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:program,holiday',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        CalendarEvent::create($validated);

        return redirect()->route('calendar.index', [
            'month' => Carbon::parse($validated['date'])->month,
            'year' => Carbon::parse($validated['date'])->year,
        ])->with('success', 'Event created successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(CalendarEvent $calendar)
    {
        // Check authorization (SUPER_ADMIN, DME_HEAD, HA_HEAD)
        if (!in_array(Auth::user()->role, [RoleType::SUPER_ADMIN, RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            abort(403, 'Unauthorized action.');
        }

        $month = $calendar->date->month;
        $year = $calendar->date->year;
        
        $calendar->delete();

        return redirect()->route('calendar.index', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Event deleted successfully.');
    }
}
