<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPlan;
use App\Models\Semester;
use Illuminate\Support\Facades\Log;
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
    public function index()
    {
        $clubId = session('club_id');
        $user = Auth::user();

        $events = Event::with(['semester', 'slot', 'classRoom', 'club'])
            ->where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $semesters = Semester::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();
        $slots = Slot::where('status', 'active')->orderBy('start_time')->get();
        $classRooms = ClassRoom::where('status', 'active')->orderBy('name')->get();

        return view('clubs.events.index', compact('events', 'semesters', 'slots', 'classRooms'));
    }

    public function store(Request $request)
    {

        $clubId = session('club_id');
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_id' => 'required|exists:semesters,id',
            'slot_id' => 'required|exists:slots,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $eventData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'club_id' => $clubId,
                'semester_id' => $validatedData['semester_id'],
                'slot_id' => $validatedData['slot_id'],
                'class_room_id' => $validatedData['class_room_id'],
                'status' => 'pending', // Default status
                'event_date' => $validatedData['event_date'],
            ];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
                $eventData['image'] = $imagePath;
            }

            $event = Event::create($eventData);

            EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
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

    public function show(Event $event)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xem sự kiện này!');
        }
        $event->load(['semester', 'slot', 'classRoom', 'club']);
        $participants = $event->checkins()->with('user')->orderBy('checkin_time', 'asc')->get();
        $participantStats = [
            'total_participants' => $participants->count(),
            'latest_checkin' => $participants->last()?->checkin_time,
            'earliest_checkin' => $participants->first()?->checkin_time,
        ];
        return view('clubs.events.show', compact('event', 'participants', 'participantStats'));
    }

    public function update(Request $request, Event $event)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa sự kiện này!');
        }
        if (!$event->canBeEdited()) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Không thể chỉnh sửa sự kiện đã hoàn thành hoặc đã hủy!');
        }
        $availableStatuses = $event->getAvailableStatusOptions($user);
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
            'event_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();
            $updateData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'semester_id' => $validatedData['semester_id'],
                'slot_id' => $validatedData['slot_id'],
                'class_room_id' => $validatedData['class_room_id'],
                'status' => $validatedData['status'],
                'event_date' => $validatedData['event_date'],
            ];
            if ($request->hasFile('image')) {
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

    public function destroy(Event $event)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa sự kiện này!');
        }
        if (!$event->canBeDeleted()) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Không thể xóa sự kiện đã được duyệt hoặc đã hoàn thành!');
        }
        try {
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

    public function generateQrCode(Event $event)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        if ($event->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền truy cập!');
        }
        if (!$event->shouldShowQRCode()) {
            return redirect()->route('backend.club.events.show', $event->id)
                ->with('error', 'QR code chỉ hiển thị trong thời gian diễn ra sự kiện và sự kiện phải được duyệt!');
        }
        $qrCode = QrCode::size(300)->generate($event->qr_hash);
        return response($qrCode, 200)->header('Content-Type', 'image/svg+xml');
    }

    public function timetable(Request $request)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        $selectedWeek = $request->get('week', date('W'));
        $selectedYear = $request->get('year', date('Y'));
        $weekOptions = [];
        for ($i = 1; $i <= 52; $i++) {
            $startOfWeek = Carbon::now()->setISODate($selectedYear, $i, 1);
            $endOfWeek = $startOfWeek->copy()->addDays(6);
            $startDate = $startOfWeek->format('d/m');
            $endDate = $endOfWeek->format('d/m');
            $weekOptions[$i] = "Tuần {$i} ({$startDate} - {$endDate})";
        }
        $slots = Slot::where('status', 'active')->orderBy('start_time')->get();
        $firstDayOfWeek = Carbon::now()->setISODate($selectedYear, $selectedWeek, 1);
        $dateHeaders = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $firstDayOfWeek->copy()->addDays($i);
            $dateHeaders[$i] = [
                'name' => ucwords($date->locale('vi')->dayName),
                'date' => $date->format('d/m'),
            ];
        }
        $startDate = $firstDayOfWeek->format('Y-m-d');
        $endDate = $firstDayOfWeek->copy()->addDays(6)->format('Y-m-d');
        $events = Event::with(['slot', 'classRoom'])
            ->where('club_id', $clubId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();
        $userCheckins = EventCheckin::whereIn('event_id', $events->pluck('id'))
            ->where('user_id', $user->id)
            ->pluck('event_id')
            ->toArray();
        $timetable = [];
        foreach ($events as $event) {
            $dayOfWeek = Carbon::parse($event->event_date)->dayOfWeek;
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
}