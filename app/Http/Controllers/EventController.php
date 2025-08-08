<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Club;
use App\Models\Semester;
use App\Models\Slot;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of all events from all clubs
     */
    public function index()
    {
        $events = Event::with(['semester', 'slot', 'classRoom', 'club'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('events.index', compact('events'));
    }

    /**
     * Display timetable view of all events from all clubs
     */
    public function timetable(Request $request)
    {
        // Get selected week and year
        $selectedWeek = $request->get('week', date('W'));
        $selectedYear = $request->get('year', date('Y'));

        // Generate week options (1-52) with date ranges
        $weekOptions = [];
        for ($i = 1; $i <= 52; $i++) {
            // Calculate start and end date for each week
            $startOfWeek = Carbon::now()->setISODate($selectedYear, $i, 1);
            $endOfWeek = $startOfWeek->copy()->addDays(6);

            $startDate = $startOfWeek->format('d/m');
            $endDate = $endOfWeek->format('d/m');

            $weekOptions[$i] = "Tuần {$i} ({$startDate} - {$endDate})";
        }

        // Get active data for timetable
        $semesters = Semester::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();

        $slots = Slot::where('status', 'active')
            ->orderBy('start_time')
            ->get();

        $clubs = Club::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Calculate dates for the selected week
        $firstDayOfWeek = Carbon::now()->setISODate($selectedYear, $selectedWeek, 1);
        $dateHeaders = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $firstDayOfWeek->copy()->addDays($i);
            $dateHeaders[$i] = [
                'name' => ucwords($date->locale('vi')->dayName),
                'date' => $date->format('d/m')
            ];
        }

        // Get events for the selected week
        $startDate = $firstDayOfWeek->format('Y-m-d');
        $endDate = $firstDayOfWeek->copy()->addDays(6)->format('Y-m-d');

        $events = Event::with(['semester', 'slot', 'classRoom', 'club'])
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        // Organize events by day of week and slot
        $timetable = [];
        foreach ($events as $event) {
            $dayOfWeek = Carbon::parse($event->event_date)->dayOfWeek;
            // Convert to Monday = 0, Sunday = 6
            $dayIndex = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;

            $timetable[$dayIndex][$event->slot_id] = $event;
        }

        return view('events.timetable', compact('timetable', 'dateHeaders', 'slots', 'semesters', 'clubs', 'selectedWeek', 'selectedYear', 'weekOptions'));
    }


    /**
     * Approve an event
     */
    public function approve(Event $event)
    {
        // Check if event is in pending status
        if ($event->status !== 'pending') {
            return redirect()->route('backend.events.index')
                ->with('error', 'Chỉ có thể phê duyệt sự kiện đang ở trạng thái chờ duyệt!');
        }

        try {
            $event->update(['status' => 'active']);

            return redirect()->route('backend.events.index')
                ->with('success', 'Sự kiện đã được phê duyệt thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi phê duyệt sự kiện: ' . $e->getMessage());
        }
    }
}
