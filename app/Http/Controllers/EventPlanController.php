<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPlan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Exports\EventPlanExport;
use Maatwebsite\Excel\Facades\Excel;

class EventPlanController extends Controller
{
    /**
     * Display a listing of event plans for the current user's club
     */
    public function index()
    {
        $eventPlans = EventPlan::orderBy('created_at', 'desc')->paginate(10);

        return view('events.event-plans.index', compact('eventPlans'));
    }

    /**
     * Display the event plan for a specific event
     */
    public function show(Event $event)
    {
        // Get event plan for this specific event with relationships
        $eventPlan = EventPlan::with([
            'event.slot',
            'event.classRoom',
            'event.semester',
            'club',
            'preparedBy',
            'objectives',
            'actionPlans.primaryResponsible',
            'budgetItems',
            'equipment',
            'timeline'
        ])
            ->where('event_id', $event->id)
            ->first();

        return view('events.event-plans.show', compact('eventPlan'));
    }

    /**
     * Export event plan to Excel
     */
    public function exportExcel(Event $event)
    {
        // Get event plan for the event with all relationships
        $eventPlan = $event->eventPlan()
            ->with([
                'event.semester',
                'event.slot',
                'event.classRoom',
                'preparedBy',
                'objectives',
                'actionPlans.primaryResponsible',
                'budgetItems',
                'equipment',
                'timeline'
            ])
            ->first();

        if (!$eventPlan) {
            return redirect()->back()
                ->with('error', 'Không tìm thấy kế hoạch sự kiện!');
        }

        $eventDateFormatted = \Carbon\Carbon::parse($event->event_date)->format('d-m-Y');
        // Remove "/" and "\" from event name and fallback
        $safeEventName = str_replace(['/', '\\'], '-', $event->name);
        $fileName = 'Kế Hoạch Sự Kiện - ' . $safeEventName . ' - ' . $eventDateFormatted . '.xlsx';

        return Excel::download(new EventPlanExport($eventPlan), $fileName);
    }
}
