<?php
// ClubController - Created based on CourseController pattern with image upload functionality
// Updated to automatically create default roles when creating a club and run ModuleClubSeeder
// Chủ Nhiệm role is assigned all permissions automatically
// Handles CRUD operations for clubs with image storage support
// Fixed show method to properly load User objects instead of Role objects for leaders/members display
// Updated role creation to include club name to avoid unique constraint violations
// Updated destroy method to delete all related data including users, events, awards, invoices and their related data
// [2024-12-19] Updated: Removed storeUser and checkUserEmail methods and related functionality
// [2024-12-19] Updated: Fixed show method to use new UserClubRole pivot table structure instead of direct role_id relationship
// [2024-12-19] Updated: Removed address field from Club model operations since address is now handled through Address model relationship
// Fixed "Attempt to read property 'province_id' on string" error by ensuring address is always a relationship object

// Added province and commune selection support with AJAX functionality for better address management

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
use App\Models\Module;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with('address.province', 'address.commune')->orderBy('created_at', 'desc')->get();
        $provinces = \App\Models\Province::all();
        return view('clubs.index', compact('clubs', 'provinces'));
    }

    public function show(Club $club)
    {
        // Get leaders for this club using the new UserClubRole pivot table
        $leaders = $club->userClubRoles()
            ->whereHas('role', function ($query) {
                $query->where('is_leader', 'leader');
            })
            ->with(['user.major', 'user.course', 'role'])
            ->get()
            ->map(function ($userClubRole) {
                $user = $userClubRole->user;
                $user->club_role = $userClubRole->role; // Add the role from pivot table
                return $user;
            });

        // Get members for this club using the new UserClubRole pivot table
        $members = $club->userClubRoles()
            ->whereHas('role', function ($query) {
                $query->where('is_leader', 'member');
            })
            ->with(['user.major', 'user.course', 'role'])
            ->get()
            ->map(function ($userClubRole) {
                $user = $userClubRole->user;
                $user->club_role = $userClubRole->role; // Add the role from pivot table
                return $user;
            });

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
            'province_id' => 'required|exists:provinces,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            $clubData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            $club = Club::create($clubData);

            // Tạo địa chỉ cho club
            $club->address()->create([
                'province_id' => $request->province_id,
                'commune_id' => $request->commune_id,
                'address' => $request->address,
            ]);

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
            // $allPermissions = Permission::all();
            // $chuNhiemRole->permissions()->attach($allPermissions->pluck('id'));

            $branchModuleIds = Module::where('type', 'branch')->pluck('id');
            $branchPermissions = Permission::whereIn('module_id', $branchModuleIds)->pluck('id');
            $chuNhiemRole->permissions()->attach($branchPermissions);

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
            'province_id' => 'required|exists:provinces,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        $clubData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        $club->update($clubData);

        // Cập nhật hoặc tạo mới địa chỉ cho club
        $address = $club->address()->firstOrNew();
        $address->fill([
            'province_id' => $request->province_id,
            'commune_id' => $request->commune_id,
            'address' => $request->address,
        ]);
        $address->save();

        return redirect()->route('backend.clubs.index')->with('success', 'Câu lạc bộ đã được cập nhật thành công!');
    }

    // Lấy danh sách xã theo tỉnh (AJAX)
    public function getCommunes(Request $request)
    {
        $provinceId = $request->province_id;
        $communes = \App\Models\Commune::where('province_id', $provinceId)->get();
        return response()->json($communes);
    }

    // Updated destroy() to handle foreign key constraint violation with sessions table when deleting users.
    public function destroy(Club $club)
    {
        try {
            DB::beginTransaction();

            // Delete all sessions of users in this club to avoid foreign key constraint errors
            $userIds = User::where('club_id', $club->id)->pluck('id');
            if ($userIds->count() > 0) {
                DB::table('sessions')->whereIn('user_id', $userIds)->delete();
            }

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

            // Delete all club images if exist (shortened)
            foreach (['image_square_light', 'image_square_dark', 'image_horizontal_light', 'image_horizontal_dark'] as $img) {
                if ($club->$img) {
                    Storage::disk('public')->delete($club->$img);
                }
            }

            $club->delete();

            DB::commit();

            return redirect()->route('backend.clubs.index')->with('success', 'Câu lạc bộ và tất cả dữ liệu liên quan đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa câu lạc bộ: ' . $e->getMessage());
        }
    }
}
