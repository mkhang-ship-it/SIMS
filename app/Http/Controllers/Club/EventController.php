<?php
// EventController - Club event management system with updated status workflow
// Handles event CRUD operations with single slot timing, classrooms, semesters, and specific event dates
// Updated to include event_date field and QR code time validation
// Updated: Implemented new status workflow (pending, active, cancelled, completed)
// Updated: Modified week options in timetable to display format "Tuần 1 (start-date - enddate)"
// Updated: Added participants list with statistics in show method to display event attendees
// Updated: Added user check-in status tracking in timetable to show check-in button only for non-participants
// Updated: Added auto-creation of EventPlan when creating events for comprehensive event planning
// Updated: Added Excel export functionality for event plans with comprehensive information including general info, objectives, action plans, budget, equipment and timeline

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPlan;
use App\Models\Semester;
use App\Models\Slot;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Models\EventCheckin;

class EventController extends Controller
{
    /**
     * Display a listing of events for current club
     */
    public function index()
    {
        $user = Auth::user();

        $events = Event::with(['semester', 'slot', 'classRoom'])
            ->where('club_id', $user->club_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get active data for modal forms
        $semesters = Semester::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();

        $slots = Slot::where('status', 'active')
            ->orderBy('start_time')
            ->get();

        $classRooms = ClassRoom::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('clubs.events.index', compact('events', 'semesters', 'slots', 'classRooms'));
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Get allowed status values for user
        $allowedStatuses = $this->getAllowedStatusesForUser();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_id' => 'required|exists:semesters,id',
            'slot_id' => 'required|exists:slots,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $eventData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'club_id' => $user->club_id,
                'semester_id' => $validatedData['semester_id'],
                'slot_id' => $validatedData['slot_id'],
                'class_room_id' => $validatedData['class_room_id'],
                'status' => $validatedData['status'],
                'event_date' => $validatedData['event_date']
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
                $eventData['image'] = $imagePath;
            }

            $event = Event::create($eventData);

            // Auto-create EventPlan for the newly created event
            EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $user->club_id,
                // Other fields are nullable and will be filled later via UI
            ]);

            DB::commit();

            return redirect()->route('backend.club.events.index')
                ->with('success', 'Sự kiện đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $user = Auth::user();

        // Check if event belongs to user's club
        if ($event->club_id !== $user->club_id) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xem sự kiện này!');
        }

        $event->load(['semester', 'slot', 'classRoom', 'club']);

        // Load participants/checkins with user information
        $participants = $event->checkins()
            ->with('user')
            ->orderBy('checkin_time', 'asc')
            ->get();

        // Get statistics
        $participantStats = [
            'total_participants' => $participants->count(),
            'latest_checkin' => $participants->last()?->checkin_time,
            'earliest_checkin' => $participants->first()?->checkin_time,
        ];

        return view('clubs.events.show', compact('event', 'participants', 'participantStats'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::user();

        // Check if event belongs to user's club
        if ($event->club_id !== $user->club_id) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa sự kiện này!');
        }

        // Check if event can be edited
        if (!$event->canBeEdited()) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Không thể chỉnh sửa sự kiện đã hoàn thành hoặc đã hủy!');
        }

        // Get available status options based on current status and user role
        $userRole = $this->getUserRole();
        $availableStatuses = $event->getAvailableStatusOptions($userRole);

        if (empty($availableStatuses)) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Không thể thay đổi trạng thái của sự kiện này!');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_id' => 'required|exists:semesters,id',
            'slot_id' => 'required|exists:slots,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'status' => 'required|in:' . implode(',', array_keys($availableStatuses)),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'semester_id' => $validatedData['semester_id'],
                'slot_id' => $validatedData['slot_id'],
                'class_room_id' => $validatedData['class_room_id'],
                'status' => $validatedData['status'],
                'event_date' => $validatedData['event_date']
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($event->image && Storage::disk('public')->exists($event->image)) {
                    Storage::disk('public')->delete($event->image);
                }

                $imagePath = $request->file('image')->store('events', 'public');
                $updateData['image'] = $imagePath;
            }

            $event->update($updateData);

            DB::commit();

            return redirect()->route('backend.club.events.index')
                ->with('success', 'Sự kiện đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();

        // Check if event belongs to user's club
        if ($event->club_id !== $user->club_id) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa sự kiện này!');
        }

        // Check if event can be deleted
        if (!$event->canBeDeleted()) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Không thể xóa sự kiện đã được duyệt hoặc đã hoàn thành!');
        }

        try {
            // Delete image if exists
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }

            $event->delete();

            return redirect()->route('backend.club.events.index')
                ->with('success', 'Sự kiện đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code for event check-in
     */
    public function generateQrCode(Event $event)
    {
        $user = Auth::user();

        // Check if event belongs to user's club
        if ($event->club_id !== $user->club_id) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        // Check if QR code should be shown
        if (!$event->shouldShowQRCode()) {
            return redirect()->route('backend.club.events.show', $event->id)
                ->with('error', 'QR code chỉ hiển thị trong thời gian diễn ra sự kiện và sự kiện phải được duyệt!');
        }

        // Generate QR code
        $qrCode = QrCode::size(300)->generate($event->qr_hash);

        return response($qrCode, 200)
            ->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Display timetable view
     */
    public function timetable(Request $request)
    {
        $user = Auth::user();

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

        // Get all active slots
        $slots = Slot::where('status', 'active')
            ->orderBy('start_time')
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

        // Get events for the selected week (show all events with color coding by status)
        $startDate = $firstDayOfWeek->format('Y-m-d');
        $endDate = $firstDayOfWeek->copy()->addDays(6)->format('Y-m-d');

        $events = Event::with(['slot', 'classRoom'])
            ->where('club_id', $user->club_id)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        // Get user's check-ins for events in this week
        $userCheckins = EventCheckin::whereIn('event_id', $events->pluck('id'))
            ->where('user_id', $user->id)
            ->pluck('event_id')
            ->toArray();

        // Organize events by day of week and slot
        $timetable = [];
        foreach ($events as $event) {
            $dayOfWeek = Carbon::parse($event->event_date)->dayOfWeek;
            // Convert to Monday = 0, Sunday = 6
            $dayIndex = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;

            $timetable[$dayIndex][$event->slot_id] = $event;
        }

        return view('clubs.events.timetable', compact(
            'slots',
            'timetable',
            'dateHeaders',
            'selectedWeek',
            'selectedYear',
            'weekOptions',
            'userCheckins'
        ));
    }

    /**
     * Get allowed statuses for user when creating events
     */
    private function getAllowedStatusesForUser()
    {
        $userRole = $this->getUserRole();

        // Admin can create events with any status except completed
        if (in_array($userRole, ['admin', 'super_admin'])) {
            return ['pending', 'active', 'cancelled'];
        }

        // Regular users can only create pending or cancelled events
        return ['pending', 'cancelled'];
    }

    /**
     * Get user role for permission checking
     */
    private function getUserRole()
    {
        $user = Auth::user();

        // You can implement your role checking logic here
        // For now, we'll assume regular users unless specified otherwise
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('super_admin')) {
                return 'super_admin';
            }
            if ($user->hasRole('admin')) {
                return 'admin';
            }
        }

        return 'user';
    }
}
