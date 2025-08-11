<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Major;
use App\Models\Course;
use App\Models\UserClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Imports\MemberImport;
use App\Exports\MemberTemplateExport;
use App\Exports\MemberExport;
use App\Models\Semester;
use App\Models\Club;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index()
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Lấy danh sách UserClubRole theo club_id, eager load user, major, course, role
        $members = UserClubRole::with(['user.major', 'user.course', 'role'])
            ->where('club_id', $clubId)
            ->get();

        // Lấy danh sách users chưa trong club để gán
        $availableUsers = User::where('type', 'branch')
            ->whereNotIn('id', function ($query) use ($clubId) {
                $query->select('user_id')
                    ->from('user_club_roles')
                    ->where('club_id', $clubId);
            })
            ->orderBy('name')
            ->get();

        $roles = Role::where('club_id', $clubId)
            ->where('status', 'active')
            ->get();

        $majors = Major::where('status', 'active')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
        $courses = Course::where('status', 'active')->get();

        return view('clubs.members.index', compact('members', 'availableUsers', 'roles', 'majors', 'courses'));
    }

    public function store(Request $request)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive'
        ]);

        $role = Role::where('id', $validatedData['role_id'])
            ->where('club_id', $clubId)
            ->first();

        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Vai trò không hợp lệ.']);
        }

        // Check if user already exists
        $existingUser = User::where('email', $validatedData['email'])->first();

        if ($existingUser) {
            // Check if user is already a member of this club
            $existingUserClubRole = UserClubRole::where('user_id', $existingUser->id)
                ->where('club_id', $clubId)
                ->first();

            if ($existingUserClubRole) {
                return redirect()->back()->withErrors(['email' => 'Thành viên này đã tồn tại trong câu lạc bộ.']);
            }

            // Assign existing user to this club
            UserClubRole::create([
                'user_id' => $existingUser->id,
                'club_id' => $clubId,
                'role_id' => $validatedData['role_id'],
                'status' => $validatedData['status'],
                'joined_at' => now(),
            ]);

            // Update default_club_id if this is the first club for the user
            if (!$existingUser->default_club_id) {
                $existingUser->update(['default_club_id' => $clubId]);
            }

            return redirect()->route('backend.club.members.index')
                ->with('success', 'Thành viên đã tồn tại trong hệ thống và đã được thêm vào câu lạc bộ thành công.');
        }

        // Create new user
        try {
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validatedData['avatar'] = $avatarPath;
            }

            $validatedData['password'] = Hash::make($validatedData['password']);

            $newUser = User::create($validatedData);

            // Assign new user to this club
            UserClubRole::create([
                'user_id' => $newUser->id,
                'club_id' => $clubId,
                'role_id' => $validatedData['role_id'],
                'status' => $validatedData['status'],
                'joined_at' => now(),
            ]);

            // Set default_club_id for new user
            $newUser->update(['default_club_id' => $clubId]);

            return redirect()->route('backend.club.members.index')
                ->with('success', 'Thành viên mới đã được tạo và thêm vào câu lạc bộ thành công.');
        } catch (\Exception $e) {
            // Delete uploaded avatar if exists
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo thành viên: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $userId)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        $member = User::findOrFail($userId);
        $userClubRole = UserClubRole::where('user_id', $userId)->where('club_id', $clubId)->first();
        if (!$userClubRole) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Không tìm thấy thành viên trong câu lạc bộ này.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($member->id)
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive'
        ]);

        $role = Role::where('id', $validatedData['role_id'])
            ->where('club_id', $clubId)
            ->first();
        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Vai trò không hợp lệ.']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($member->avatar && Storage::disk('public')->exists($member->avatar)) {
                Storage::disk('public')->delete($member->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $member->avatar = $avatarPath;
        }

        // Update user fields
        $member->name = $validatedData['name'];
        $member->email = $validatedData['email'];
        $member->gender = $validatedData['gender'] ?? null;
        $member->birthday = $validatedData['birthday'] ?? null;
        $member->identity_card_code = $validatedData['identity_card_code'] ?? null;
        $member->phone = $validatedData['phone'] ?? null;
        $member->student_code = $validatedData['student_code'] ?? null;
        $member->major_id = $validatedData['major_id'] ?? null;
        $member->course_id = $validatedData['course_id'] ?? null;
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $member->password = Hash::make($request->password);
        }
        $member->save();

        // Update UserClubRole fields
        $userClubRole->role_id = $validatedData['role_id'];
        $userClubRole->status = $validatedData['status'];
        $userClubRole->save();

        return redirect()->route('backend.club.members.index')
            ->with('success', 'Thành viên đã được cập nhật thành công.');
    }

    public function destroy(User $member)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Check if user is trying to remove themselves
        if ($member->id === $user->id) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không thể tự xóa chính mình khỏi câu lạc bộ.');
        }

        // Find the UserClubRole record for this member in this club
        $userClubRole = UserClubRole::where('user_id', $member->id)
            ->where('club_id', $clubId)
            ->first();

        if (!$userClubRole) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Không tìm thấy thành viên trong câu lạc bộ này.');
        }

        // Check if the member has admin role in this club
        if ($userClubRole->role && $userClubRole->role->is_admin == true) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không thể xóa thành viên có vai trò là quản trị viên.');
        }

        try {
            // Delete related data for this user in this specific club
            // Delete award recipients for this user in this club
            \App\Models\AwardRecipient::where('user_club_role_id', $userClubRole->id)
                ->delete();

            // Delete invoices and their transactions for this user in this club
            $invoices = \App\Models\Invoice::where('user_id', $member->id)
                ->where('club_id', $clubId)
                ->get();
            foreach ($invoices as $invoice) {
                $invoice->transactions()->delete();
                $invoice->delete();
            }

            // Delete event checkins for this user in this club
            \App\Models\EventCheckin::where('user_id', $member->id)
                ->whereHas('event', function ($query) use ($clubId) {
                    $query->where('club_id', $clubId);
                })
                ->delete();

            // Delete the UserClubRole record (remove from club only)
            $userClubRole->delete();

            return redirect()->route('backend.club.members.index')
                ->with('success', 'Thành viên đã được xóa khỏi câu lạc bộ thành công.');
        } catch (\Exception $e) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Có lỗi xảy ra khi xóa thành viên khỏi câu lạc bộ: ' . $e->getMessage());
        }
    }

    public function checkUserEmail(Request $request)
    {
        $email = $request->query('email');
        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function assignToClub(Request $request)
    {
        $clubId = session('club_id');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Check if role belongs to the club
            $role = Role::where('id', $request->role_id)
                ->where('club_id', $clubId)
                ->where('status', 'active')
                ->first();

            if (!$role) {
                return redirect()->back()->with('error', 'Vai trò không hợp lệ cho câu lạc bộ này!');
            }

            // Check if user is already assigned to this club
            $exists = UserClubRole::where('user_id', $request->user_id)
                ->where('club_id', $clubId)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Thành viên này đã được gán vào câu lạc bộ!');
            }

            // Assign user to club
            UserClubRole::create([
                'user_id' => $request->user_id,
                'club_id' => $clubId,
                'role_id' => $request->role_id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // Update default_club_id if this is the first club for the user
            $user = User::find($request->user_id);
            if ($user && !$user->default_club_id) {
                $user->update(['default_club_id' => $clubId]);
            }

            return redirect()->route('backend.club.members.index')->with('success', 'Thành viên đã được gán vào câu lạc bộ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gán thành viên: ' . $e->getMessage());
        }
    }

    /**
     * Import members from Excel file
     */
    public function import(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv'
        ], [
            'import_file.required' => 'Vui lòng chọn file để import.',
            'import_file.file' => 'File upload không hợp lệ.',
            'import_file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls) hoặc CSV.'
        ]);

        try {
            $import = new MemberImport($clubId);
            Excel::import($import, $request->file('import_file'));

            $importedCount = $import->getImportedCount();
            $addedToClubCount = $import->getAddedToClubCount();
            $errors = $import->getErrors();

            $message = "";
            if ($importedCount > 0) {
                $message .= "Tạo mới thành công {$importedCount} tài khoản! ";
            }
            if ($addedToClubCount > 0) {
                $message .= "Thêm vào câu lạc bộ thành công {$addedToClubCount} thành viên! ";
            }

            if (empty($message)) {
                $message = "Không có thành viên nào được import.";
            }

            if (!empty($errors)) {
                $errorMessage = implode("<br>", $errors);
                $message .= " Một số lỗi xảy ra:<br>" . $errorMessage;

                return redirect()->route('backend.club.members.index')
                    ->with('warning', $message);
            }

            return redirect()->route('backend.club.members.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Dòng {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->with('error', 'Lỗi validation khi import:\n' . implode('\n', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }

    /**
     * Export member import template
     */
    public function exportTemplate()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        try {
            return Excel::download(
                new MemberTemplateExport($clubId),
                'File Mẫu Import Thành Viên.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải file mẫu: ' . $e->getMessage());
        }
    }

    /**
     * Export members data to Excel
     */
    public function exportMembersExcel(Request $request)
    {
        $clubId = session('club_id');
        $semesterId = $request->query('semester_id') ?? Semester::getCurrentSemester()?->id;

        // Get club name for filename
        $club = Club::find($clubId);
        $clubName = $club ? $club->name : 'CLB';

        $filename = 'Danh Sách Thành Viên - ' . $clubName . '.xlsx';

        try {
            return Excel::download(
                new MemberExport($clubId, $semesterId, $clubName),
                $filename
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xuất dữ liệu: ' . $e->getMessage());
        }
    }
}
