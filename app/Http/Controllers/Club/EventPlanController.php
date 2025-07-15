<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPlan;
use App\Models\EventPlanObjective;
use App\Models\EventActionPlan;
use App\Models\EventPlanBudgetItem;
use App\Models\EventPlanEquipment;
use App\Models\EventPlanTimeline;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Exports\EventPlanExport;
use Maatwebsite\Excel\Facades\Excel;

// Refactored: EventPlanController - Updated for new DB structure (multi-club, UserClubRole, session('club_id'))
// - All club scoping uses session('club_id')
// - Fetches leaders and members via UserClubRole (eager loads user, role)
// - No direct User::where('club_id', ...) queries
// - All view data for leaders/members uses UserClubRole, not User
// - Updated all relationships and fillable fields to match new models
// - No business logic changed, only DB structure and relationship updates
class EventPlanController extends Controller
{
    /**
     * Display the event plan for a specific event
     */
    public function show(Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xem kế hoạch sự kiện này!');
        }

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

        // If no event plan exists, create one
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
            $eventPlan->load([
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
            ]);
        }

        // Get leaders from current club for prepared_by dropdown (UserClubRole)
        $leaderRoleIds = \App\Models\Role::where('club_id', $clubId)
            ->where('is_leader', 'leader')
            ->where('status', 'active')
            ->pluck('id');
        $leaders = \App\Models\UserClubRole::with(['user', 'role'])
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->whereIn('role_id', $leaderRoleIds)
            ->orderByRaw('(SELECT name FROM users WHERE users.id = user_club_roles.user_id)')
            ->get();

        // Get all active club members for action plan responsibilities (UserClubRole)
        $clubMembers = \App\Models\UserClubRole::with(['user', 'role'])
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->orderByRaw('(SELECT name FROM users WHERE users.id = user_club_roles.user_id)')
            ->get();

        return view('clubs.event-plans.show', compact('eventPlan', 'leaders', 'clubMembers'));
    }

    /**
     * Update the event plan for a specific event
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật kế hoạch sự kiện này!');
        }

        // Get event plan for this specific event
        $eventPlan = EventPlan::where('event_id', $event->id)->first();

        // If no event plan exists, create one
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'prepared_by' => 'nullable|exists:users,id',
            'implementation_time' => 'nullable|date',
            'approved_by' => 'nullable|string',
            'participants' => 'nullable|string',
            'description' => 'nullable|string',
            'responsible_organizer' => 'nullable|string',
            'advisor_coordinator' => 'nullable|string',
        ]);

        try {
            // Update the event plan
            $eventPlan->update($validatedData);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Kế hoạch sự kiện đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật kế hoạch sự kiện: ' . $e->getMessage());
        }
    }

    /**
     * Store a new objective for the event plan
     */
    public function storeObjective(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm mục tiêu cho sự kiện này!');
        }

        // Get or create event plan
        $eventPlan = EventPlan::where('event_id', $event->id)->first();
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'objective' => 'required|string|max:1000',
            'expected_result' => 'required|string|max:1000',
            'responsible_party' => 'required|string|max:255',
        ]);

        try {
            EventPlanObjective::create([
                'event_plan_id' => $eventPlan->id,
                'objective' => $validatedData['objective'],
                'expected_result' => $validatedData['expected_result'],
                'responsible_party' => $validatedData['responsible_party'],
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục tiêu đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm mục tiêu: ' . $e->getMessage());
        }
    }

    /**
     * Update an objective
     */
    public function updateObjective(Request $request, Event $event, EventPlanObjective $objective)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật mục tiêu này!');
        }

        // Validate the request
        $validatedData = $request->validate([
            'objective' => 'required|string|max:1000',
            'expected_result' => 'required|string|max:1000',
            'responsible_party' => 'required|string|max:255',
        ]);

        try {
            $objective->update($validatedData);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục tiêu đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật mục tiêu: ' . $e->getMessage());
        }
    }

    /**
     * Delete an objective
     */
    public function destroyObjective(Event $event, EventPlanObjective $objective)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa mục tiêu này!');
        }

        try {
            $objective->delete();

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục tiêu đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa mục tiêu: ' . $e->getMessage());
        }
    }

    /**
     * Store a new action plan for the event plan
     */
    public function storeActionPlan(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm kế hoạch hành động cho sự kiện này!');
        }

        // Get or create event plan
        $eventPlan = EventPlan::where('event_id', $event->id)->first();
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'activity_item' => 'required|string|max:255',
            'expected_result' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'primary_responsible_id' => 'required|exists:users,id',
            'support_members' => 'nullable|array',
            'support_members.*' => 'exists:users,id',
            'potential_risks' => 'nullable|string',
            'risk_prevention_actions' => 'nullable|string',
        ]);

        try {
            EventActionPlan::create([
                'event_plan_id' => $eventPlan->id,
                'activity_item' => $validatedData['activity_item'],
                'expected_result' => $validatedData['expected_result'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'primary_responsible_id' => $validatedData['primary_responsible_id'],
                'support_members' => $validatedData['support_members'] ?? [],
                'potential_risks' => $validatedData['potential_risks'],
                'risk_prevention_actions' => $validatedData['risk_prevention_actions'],
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Kế hoạch hành động đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm kế hoạch hành động: ' . $e->getMessage());
        }
    }

    /**
     * Update an action plan
     */
    public function updateActionPlan(Request $request, Event $event, EventActionPlan $actionPlan)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật kế hoạch hành động này!');
        }

        // Validate the request
        $validatedData = $request->validate([
            'activity_item' => 'required|string|max:255',
            'expected_result' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'primary_responsible_id' => 'required|exists:users,id',
            'support_members' => 'nullable|array',
            'support_members.*' => 'exists:users,id',
            'potential_risks' => 'nullable|string',
            'risk_prevention_actions' => 'nullable|string',
        ]);

        try {
            $actionPlan->update([
                'activity_item' => $validatedData['activity_item'],
                'expected_result' => $validatedData['expected_result'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'primary_responsible_id' => $validatedData['primary_responsible_id'],
                'support_members' => $validatedData['support_members'] ?? [],
                'potential_risks' => $validatedData['potential_risks'],
                'risk_prevention_actions' => $validatedData['risk_prevention_actions'],
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Kế hoạch hành động đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật kế hoạch hành động: ' . $e->getMessage());
        }
    }

    /**
     * Delete an action plan
     */
    public function destroyActionPlan(Event $event, EventActionPlan $actionPlan)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa kế hoạch hành động này!');
        }

        try {
            $actionPlan->delete();

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Kế hoạch hành động đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa kế hoạch hành động: ' . $e->getMessage());
        }
    }

    /**
     * Store a new budget item for the event plan
     */
    public function storeBudgetItem(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm mục ngân sách cho sự kiện này!');
        }

        // Get or create event plan
        $eventPlan = EventPlan::where('event_id', $event->id)->first();
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'responsible_person' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            EventPlanBudgetItem::create([
                'event_plan_id' => $eventPlan->id,
                'content' => $validatedData['content'],
                'quantity' => $validatedData['quantity'],
                'unit' => $validatedData['unit'],
                'unit_price' => $validatedData['unit_price'],
                'responsible_person' => $validatedData['responsible_person'],
                'notes' => $validatedData['notes'],
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục ngân sách đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm mục ngân sách: ' . $e->getMessage());
        }
    }

    /**
     * Update a budget item
     */
    public function updateBudgetItem(Request $request, Event $event, EventPlanBudgetItem $budgetItem)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật mục ngân sách này!');
        }

        // Validate the request
        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'responsible_person' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $budgetItem->update($validatedData);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục ngân sách đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật mục ngân sách: ' . $e->getMessage());
        }
    }

    /**
     * Delete a budget item
     */
    public function destroyBudgetItem(Event $event, EventPlanBudgetItem $budgetItem)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa mục ngân sách này!');
        }

        try {
            $budgetItem->delete();

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Mục ngân sách đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa mục ngân sách: ' . $e->getMessage());
        }
    }

    /**
     * Store a new equipment item for the event plan
     */
    public function storeEquipment(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm vật dụng cho sự kiện này!');
        }

        // Get or create event plan
        $eventPlan = EventPlan::where('event_id', $event->id)->first();
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:club,school',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            EventPlanEquipment::create([
                'event_plan_id' => $eventPlan->id,
                'name' => $validatedData['name'],
                'quantity' => $validatedData['quantity'],
                'type' => $validatedData['type'],
                'notes' => $validatedData['notes'],
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Vật dụng đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm vật dụng: ' . $e->getMessage());
        }
    }

    /**
     * Update an equipment item
     */
    public function updateEquipment(Request $request, Event $event, EventPlanEquipment $equipment)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật vật dụng này!');
        }

        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:club,school',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $equipment->update($validatedData);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Vật dụng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật vật dụng: ' . $e->getMessage());
        }
    }

    /**
     * Delete an equipment item
     */
    public function destroyEquipment(Event $event, EventPlanEquipment $equipment)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa vật dụng này!');
        }

        try {
            $equipment->delete();

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Vật dụng đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa vật dụng: ' . $e->getMessage());
        }
    }

    /**
     * Store a new timeline item for the event plan
     */
    public function storeTimeline(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm timeline cho sự kiện này!');
        }

        // Get or create event plan
        $eventPlan = EventPlan::where('event_id', $event->id)->first();
        if (!$eventPlan) {
            $eventPlan = EventPlan::create([
                'event_id' => $event->id,
                'club_id' => $clubId,
            ]);
        }

        // Validate the request
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'content' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Get the next order index
            $maxOrder = EventPlanTimeline::where('event_plan_id', $eventPlan->id)->max('order_index') ?? 0;

            EventPlanTimeline::create([
                'event_plan_id' => $eventPlan->id,
                'date' => $validatedData['date'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'content' => $validatedData['content'],
                'notes' => $validatedData['notes'],
                'order_index' => $maxOrder + 1,
            ]);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Timeline đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm timeline: ' . $e->getMessage());
        }
    }

    /**
     * Update a timeline item
     */
    public function updateTimeline(Request $request, Event $event, EventPlanTimeline $timeline)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền cập nhật timeline này!');
        }

        // Validate the request
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'content' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $timeline->update($validatedData);

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Timeline đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật timeline: ' . $e->getMessage());
        }
    }

    /**
     * Delete a timeline item
     */
    public function destroyTimeline(Event $event, EventPlanTimeline $timeline)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền xóa timeline này!');
        }

        try {
            $timeline->delete();

            return redirect()->route('backend.club.event-plans.show', $event->id)
                ->with('success', 'Timeline đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa timeline: ' . $e->getMessage());
        }
    }

    /**
     * Update timeline items order (for drag and drop)
     */
    public function updateTimelineOrder(Request $request, Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validatedData = $request->validate([
            'timeline_ids' => 'required|array',
            'timeline_ids.*' => 'required|string|exists:event_plan_timeline,id',
        ]);

        try {
            // Update order for each timeline item
            foreach ($validatedData['timeline_ids'] as $index => $timelineId) {
                EventPlanTimeline::where('id', $timelineId)->update([
                    'order_index' => $index + 1
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Thứ tự timeline đã được cập nhật!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export event plan to Excel
     */
    public function exportExcel(Event $event)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if event belongs to user's club
        if ($event->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền truy cập sự kiện này!');
        }

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
