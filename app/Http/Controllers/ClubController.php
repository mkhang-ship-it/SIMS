<?php
// ClubController - Created based on CourseController pattern with image upload functionality
// Updated to automatically create default roles when creating a club and run ModuleClubSeeder
// Chủ Nhiệm role is assigned all permissions automatically
// Handles CRUD operations for clubs with image storage support
// Added storeUser method to handle adding new users to specific clubs
// Fixed show method to properly load User objects instead of Role objects for leaders/members display
// Updated role creation to include club name to avoid unique constraint violations
// Updated destroy method to delete all related data including users, events, awards, invoices and their related data

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ModuleClubSeeder;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\Award;
use App\Models\Invoice;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::orderBy('created_at', 'desc')->get();
        return view('clubs.index', compact('clubs'));
    }

    public function show(Club $club)
    {
        // Get leader roles for this club
        $leaderRoles = $club->roles()->where('is_leader', 'leader')->pluck('id');
        $leaders = User::whereIn('role_id', $leaderRoles)->with(['role', 'major', 'course'])->get();

        // Get member roles for this club
        $memberRoles = $club->roles()->where('is_leader', 'member')->pluck('id');
        $members = User::whereIn('role_id', $memberRoles)->with(['role', 'major', 'course'])->get();

        // Get events for this club
        $events = Event::where('club_id', $club->id)->where('status', 'completed')->get();

        return view('clubs.show', compact('club', 'leaders', 'members', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clubs,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'status' => 'required|string|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $clubData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('clubs', $imageName, 'public');
                $clubData['image'] = $imagePath;
            }

            $club = Club::create($clubData);

            // Run ModuleClubSeeder to ensure modules and permissions exist
            $seeder = new ModuleClubSeeder();
            $seeder->run();

            // Automatically create default roles for the club
            $chuNhiemRole = Role::create([
                'name' => 'Chủ Nhiệm',
                'type' => 'branch',
                'club_id' => $club->id,
                'status' => 'active',
                'is_default' => true,
                'is_leader' => 'leader',
                'is_admin' => true
            ]);

            $thanhVienRole = Role::create([
                'name' => 'Thành Viên',
                'type' => 'branch',
                'club_id' => $club->id,
                'status' => 'active',
                'is_default' => true,
                'is_leader' => 'member',
                'is_admin' => false
            ]);

            // Assign all permissions to the "Chủ Nhiệm" role
            $allPermissions = Permission::all();
            $chuNhiemRole->permissions()->attach($allPermissions->pluck('id'));

            DB::commit();

            return redirect()->route('backend.clubs.index')->with('success', 'Câu lạc bộ, các vai trò mặc định và quyền đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded image if exists
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo câu lạc bộ: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clubs,email,' . $club->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'status' => 'required|string|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $clubData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($club->image) {
                Storage::disk('public')->delete($club->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('clubs', $imageName, 'public');
            $clubData['image'] = $imagePath;
        }

        $club->update($clubData);

        return redirect()->route('backend.clubs.index')->with('success', 'Câu lạc bộ đã được cập nhật thành công!');
    }

    public function destroy(Club $club)
    {
        try {
            DB::beginTransaction();

            // Delete all users associated with this club
            $users = User::where('club_id', $club->id)->get();
            foreach ($users as $user) {
                // Delete user's award recipients
                $user->awardRecipients()->delete();

                // Delete user's invoices and their transactions
                foreach ($user->invoices as $invoice) {
                    $invoice->transactions()->delete();
                    $invoice->delete();
                }

                // Delete user's event checkins
                $user->checkins()->delete();

                // Delete user's avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $user->delete();
            }

            // Delete all events and their related data
            $events = Event::where('club_id', $club->id)->get();
            foreach ($events as $event) {
                // Delete event checkins
                $event->checkins()->delete();

                // Delete event images
                foreach ($event->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // Delete event plan and its related data
                if ($event->eventPlan) {
                    $eventPlan = $event->eventPlan;

                    // Delete event plan objectives
                    $eventPlan->objectives()->delete();

                    // Delete event plan action plans
                    $eventPlan->actionPlans()->delete();

                    // Delete event plan budget items
                    $eventPlan->budgetItems()->delete();

                    // Delete event plan equipment
                    $eventPlan->equipment()->delete();

                    // Delete event plan timeline
                    $eventPlan->timeline()->delete();

                    $eventPlan->delete();
                }

                // Delete event image if exists
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }

                $event->delete();
            }

            // Delete all awards and their recipients
            $awards = Award::where('club_id', $club->id)->get();
            foreach ($awards as $award) {
                // Delete award recipients
                $award->awardRecipients()->delete();

                $award->delete();
            }

            // Delete all invoices and their transactions
            $invoices = Invoice::where('club_id', $club->id)->get();
            foreach ($invoices as $invoice) {
                $invoice->transactions()->delete();
                $invoice->delete();
            }

            // Delete all roles associated with this club
            foreach ($club->roles as $role) {
                // Detach all permissions from roles
                $role->permissions()->detach();
                $role->delete();
            }

            // Delete club image if exists
            if ($club->image) {
                Storage::disk('public')->delete($club->image);
            }

            $club->delete();

            DB::commit();

            return redirect()->route('backend.clubs.index')->with('success', 'Câu lạc bộ và tất cả dữ liệu liên quan đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa câu lạc bộ: ' . $e->getMessage());
        }
    }

    /**
     * Store a new user for a specific club
     */
    public function storeUser(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Verify that the role belongs to the current club and is of type 'branch'
            $role = Role::where('id', $request->role_id)
                ->where('club_id', $club->id)
                ->where('type', 'branch')
                ->where('status', 'active')
                ->first();

            if (!$role) {
                return redirect()->back()->with('error', 'Vai trò được chọn không hợp lệ cho câu lạc bộ này!');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'club_id' => $club->id,
                'status' => 'active',
            ]);

            return redirect()->back()->with('success', 'Thành viên mới đã được thêm vào câu lạc bộ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm thành viên: ' . $e->getMessage());
        }
    }
}
