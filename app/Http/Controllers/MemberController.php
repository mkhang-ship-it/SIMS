<?php
// MemberController - Created for admin member management with multi-club support
// Handles CRUD operations for members with modal-based interface
// Supports assigning existing users to clubs, updating member information, and removing members from clubs
// When removing a member from a club, all related data (awards, invoices, checkins) for that club are also deleted
// [2024-12-19] Updated: Admin destroy method completely deletes user from system (all clubs and related data)
// [2024-12-19] Fixed: Award recipient deletion to use user_club_role_id instead of user_id column

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Role;
use App\Models\UserClubRole;
use App\Models\Major;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\AwardRecipient;
use App\Models\Invoice;
use App\Models\EventCheckin;
use App\Models\Semester;
use App\Exports\CentralMemberExport;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index()
    {
        // Get all users with their club memberships
        $members = User::with(['userClubRoles.club', 'userClubRoles.role', 'major', 'course'])
            ->where('type', 'branch')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all clubs for assignment
        $clubs = Club::where('status', 'active')->orderBy('name')->get();

        // Get all roles for assignment
        $roles = Role::where('type', 'branch')
            ->where('status', 'active')
            ->with('club')
            ->orderBy('name')
            ->get();

        // Get majors and courses for form
        $majors = Major::where('status', 'active')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('members.index', compact('members', 'clubs', 'roles', 'majors', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'identity_card_code' => $request->identity_card_code,
                'phone' => $request->phone,
                'student_code' => $request->student_code,
                'major_id' => $request->major_id,
                'course_id' => $request->course_id,
                'status' => $request->status,
                'type' => 'branch',
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user = User::create($userData);

            DB::commit();

            return redirect()->route('backend.members.index')->with('success', 'Thành viên đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded avatar if exists
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo thành viên: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $member->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'identity_card_code' => $request->identity_card_code,
                'phone' => $request->phone,
                'student_code' => $request->student_code,
                'major_id' => $request->major_id,
                'course_id' => $request->course_id,
                'status' => $request->status,
                'type' => 'branch',
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $userData['avatar'] = $avatarPath;
            }

            $member->update($userData);

            DB::commit();

            return redirect()->route('backend.members.index')->with('success', 'Thông tin thành viên đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded avatar if exists
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật thành viên: ' . $e->getMessage());
        }
    }

    public function destroy(User $member)
    {
        try {
            DB::beginTransaction();

            // Completely delete user from system - removes from all clubs and deletes all related data
            $userClubRoles = $member->userClubRoles;
            foreach ($userClubRoles as $userClubRole) {
                $clubId = $userClubRole->club_id;

                // Delete award recipients for this user in this club
                AwardRecipient::where('user_club_role_id', $userClubRole->id)
                    ->delete();

                // Delete invoices and their transactions for this user in this club
                $invoices = Invoice::where('user_id', $member->id)
                    ->where('club_id', $clubId)
                    ->get();
                foreach ($invoices as $invoice) {
                    $invoice->transactions()->delete();
                    $invoice->delete();
                }

                // Delete event checkins for this user in this club
                EventCheckin::where('user_id', $member->id)
                    ->whereHas('event', function ($query) use ($clubId) {
                        $query->where('club_id', $clubId);
                    })
                    ->delete();
            }

            // Handle expenses - set requested_by and approved_by to null to preserve expense records
            DB::table('expenses')
                ->where('requested_by', $member->id)
                ->update(['requested_by' => null]);

            DB::table('expenses')
                ->where('approved_by', $member->id)
                ->update(['approved_by' => null]);

            // Delete all user club roles
            $member->userClubRoles()->delete();

            // Delete user's avatar if exists
            if ($member->avatar) {
                Storage::disk('public')->delete($member->avatar);
            }

            // Delete user
            $member->delete();

            DB::commit();

            return redirect()->route('backend.members.index')->with('success', 'Thành viên đã được xóa hoàn toàn khỏi hệ thống!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa thành viên khỏi hệ thống: ' . $e->getMessage());
        }
    }

    public function assignToClub(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'club_id' => 'required|exists:clubs,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Check if role belongs to the club
            $role = Role::where('id', $request->role_id)
                ->where('club_id', $request->club_id)
                ->where('type', 'branch')
                ->where('status', 'active')
                ->first();

            if (!$role) {
                return redirect()->back()->with('error', 'Vai trò không hợp lệ cho câu lạc bộ này!');
            }

            // Check if user is already assigned to this club
            $exists = UserClubRole::where('user_id', $request->user_id)
                ->where('club_id', $request->club_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Thành viên này đã được gán vào câu lạc bộ!');
            }

            // Assign user to club
            UserClubRole::create([
                'user_id' => $request->user_id,
                'club_id' => $request->club_id,
                'role_id' => $request->role_id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // Update default_club_id if this is the first club for the user
            $user = User::find($request->user_id);
            if ($user && !$user->default_club_id) {
                $user->update(['default_club_id' => $request->club_id]);
            }

            return redirect()->route('backend.members.index')->with('success', 'Thành viên đã được gán vào câu lạc bộ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gán thành viên: ' . $e->getMessage());
        }
    }

    public function removeFromClub(Request $request)
    {
        $request->validate([
            'user_club_role_id' => 'required|exists:user_club_roles,id',
        ]);

        try {
            DB::beginTransaction();

            $userClubRole = UserClubRole::findOrFail($request->user_club_role_id);
            $userId = $userClubRole->user_id;
            $clubId = $userClubRole->club_id;

            // Delete award recipients for this user in this club
            AwardRecipient::where('user_club_role_id', $userClubRole->id)
                ->delete();

            // Delete invoices and their transactions for this user in this club
            $invoices = Invoice::where('user_id', $userId)
                ->where('club_id', $clubId)
                ->get();
            foreach ($invoices as $invoice) {
                $invoice->transactions()->delete();
                $invoice->delete();
            }

            // Delete event checkins for this user in this club
            EventCheckin::where('user_id', $userId)
                ->whereHas('event', function ($query) use ($clubId) {
                    $query->where('club_id', $clubId);
                })
                ->delete();

            // Delete the user club role
            $userClubRole->delete();

            DB::commit();

            return redirect()->route('backend.members.index')->with('success', 'Thành viên đã được gỡ khỏi câu lạc bộ thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gỡ thành viên: ' . $e->getMessage());
        }
    }

    public function updateClubRole(Request $request)
    {
        $request->validate([
            'user_club_role_id' => 'required|exists:user_club_roles,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $userClubRole = UserClubRole::findOrFail($request->user_club_role_id);

            // Check if new role belongs to the same club
            $role = Role::where('id', $request->role_id)
                ->where('club_id', $userClubRole->club_id)
                ->where('type', 'branch')
                ->where('status', 'active')
                ->first();

            if (!$role) {
                return redirect()->back()->with('error', 'Vai trò không hợp lệ cho câu lạc bộ này!');
            }

            $userClubRole->update([
                'role_id' => $request->role_id,
            ]);

            return redirect()->route('backend.members.index')->with('success', 'Vai trò của thành viên đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật vai trò: ' . $e->getMessage());
        }
    }

    public function getClubRoles(Request $request)
    {
        $clubId = $request->query('club_id');
        $roles = Role::where('club_id', $clubId)
            ->where('type', 'branch')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($roles);
    }

    public function checkUserEmail(Request $request)
    {
        $email = $request->query('email');
        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Export members data to Excel
     */
    public function exportMembersExcel(Request $request)
    {
        $clubId = $request->query('club_id');
        $semesterId = $request->query('semester_id') ?? Semester::getCurrentSemester()?->id;

        $filename = 'Danh Sách Thành Viên.xlsx';

        return Excel::download(
            new CentralMemberExport($clubId, $semesterId),
            $filename
        );
    }
}
