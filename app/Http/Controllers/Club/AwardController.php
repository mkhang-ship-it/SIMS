<?php
// Updated AwardController - Integrated recipient management using UserClubRole (user_club_role_id) instead of User (user_id)
// Handles both award CRUD and recipient management in one controller

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Award;
use App\Models\User;
use App\Models\Semester;
use App\Models\AwardRecipient;
use App\Models\UserClubRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AwardController extends Controller
{
    /**
     * Display a listing of awards for current club
     */
    public function index()
    {
        $user = Auth::user();

        $clubId = session('club_id');

        $awards = Award::with(['semester', 'awardRecipients.userClubRole.user'])
            ->where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // or any number per page

        // Get active semesters for modal form
        $semesters = Semester::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('clubs.awards.index', compact('awards', 'semesters'));
    }

    /**
     * Store a newly created award
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'semester_id' => 'required|exists:semesters,id',
            'award_date' => 'required|date',
            'status' => 'required|in:pending,awarded,revoked',
            'notes' => 'nullable|string'
        ]);

        try {
            $awardData = [
                'title' => $validatedData['title'],
                'club_id' => $user->club_id,
                'semester_id' => $validatedData['semester_id'],
                'award_date' => $validatedData['award_date'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes']
            ];

            Award::create($awardData);

            return redirect()->route('backend.club.awards.index')
                ->with('success', 'Giải thưởng đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo giải thưởng: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified award with recipients management
     */
    public function show(Award $award)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->route('backend.club.awards.index')
                ->with('error', 'Bạn không có quyền xem giải thưởng này!');
        }

        $award->load(['semester', 'awardRecipients.userClubRole.user', 'awardRecipients.userClubRole.role']);

        // Get available UserClubRoles for adding as recipients (active members in the same club who are not already recipients)
        $existingRecipientIds = $award->awardRecipients->pluck('user_club_role_id')->toArray();

        $availableUserClubRoles = UserClubRole::with(['user', 'role'])
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->whereNotIn('id', $existingRecipientIds)
            ->orderByRaw('(SELECT name FROM users WHERE users.id = user_club_roles.user_id)')
            ->get();

        return view('clubs.awards.show', compact('award', 'availableUserClubRoles'));
    }

    /**
     * Update the specified award
     */
    public function update(Request $request, Award $award)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->route('backend.club.awards.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa giải thưởng này!');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'semester_id' => 'required|exists:semesters,id',
            'award_date' => 'required|date',
            'status' => 'required|in:pending,awarded,revoked',
            'notes' => 'nullable|string'
        ]);

        try {
            $updateData = [
                'title' => $validatedData['title'],
                'semester_id' => $validatedData['semester_id'],
                'award_date' => $validatedData['award_date'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes']
            ];

            $award->update($updateData);

            return redirect()->route('backend.club.awards.index')
                ->with('success', 'Giải thưởng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật giải thưởng: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified award
     */
    public function destroy(Award $award)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->route('backend.club.awards.index')
                ->with('error', 'Bạn không có quyền xóa giải thưởng này!');
        }

        try {
            DB::beginTransaction();

            // Delete all award recipients and their attachments
            foreach ($award->awardRecipients as $recipient) {
                if ($recipient->attachment && Storage::disk('public')->exists($recipient->attachment)) {
                    Storage::disk('public')->delete($recipient->attachment);
                }
                $recipient->delete();
            }

            // Delete award
            $award->delete();

            DB::commit();

            return redirect()->route('backend.club.awards.index')
                ->with('success', 'Giải thưởng đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa giải thưởng: ' . $e->getMessage());
        }
    }

    /**
     * Store a new recipient for an award
     */
    public function storeRecipient(Request $request, Award $award)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền thêm người nhận cho giải thưởng này!');
        }

        $validatedData = $request->validate([
            'user_club_role_id' => 'required|exists:user_club_roles,id',
            'attachment' => 'nullable|file|mimes:pdf|max:5120' // 5MB
        ]);

        try {
            // Check if user_club_role is already a recipient
            if ($award->awardRecipients()->where('user_club_role_id', $validatedData['user_club_role_id'])->exists()) {
                return redirect()->back()->with('error', 'Thành viên vừa chọn đã là người nhận của giải thưởng này!');
            }

            // Check if user_club_role belongs to the same club
            $selectedUCR = UserClubRole::find($validatedData['user_club_role_id']);
            if ($selectedUCR->club_id !== $clubId) {
                return redirect()->back()->with('error', 'Thành viên vừa chọn không thuộc câu lạc bộ của bạn!');
            }

            $recipientData = [
                'award_id' => $award->id,
                'user_club_role_id' => $validatedData['user_club_role_id']
            ];

            // Handle file upload if provided
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = $award->award_code . '_' . $selectedUCR->user->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('award_certificates', $fileName, 'public');
                $recipientData['attachment'] = $filePath;
            }

            AwardRecipient::create($recipientData);

            return redirect()->back()->with('success', 'Người nhận đã được thêm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update a recipient's information
     */
    public function updateRecipient(Request $request, Award $award, AwardRecipient $recipient)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật người nhận cho giải thưởng này!');
        }

        // Check if recipient belongs to this award
        if ($recipient->award_id !== $award->id) {
            return redirect()->back()->with('error', 'Người nhận không thuộc giải thưởng này!');
        }

        $validatedData = $request->validate([
            'attachment' => 'nullable|file|mimes:pdf|max:5120' // 5MB
        ]);

        try {
            // Handle file upload if provided
            if ($request->hasFile('attachment')) {
                // Delete old file if exists
                if ($recipient->attachment && Storage::disk('public')->exists($recipient->attachment)) {
                    Storage::disk('public')->delete($recipient->attachment);
                }

                $file = $request->file('attachment');
                $selectedUCR = $recipient->userClubRole;
                $fileName = $award->award_code . '_' . $selectedUCR->user->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('award_certificates', $fileName, 'public');

                $recipient->update(['attachment' => $filePath]);
            }

            return redirect()->back()->with('success', 'Thông tin người nhận đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove a recipient from an award
     */
    public function destroyRecipient(Award $award, AwardRecipient $recipient)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if award belongs to user's club
        if ($award->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa người nhận khỏi giải thưởng này!');
        }

        // Check if recipient belongs to this award
        if ($recipient->award_id !== $award->id) {
            return redirect()->back()->with('error', 'Người nhận không thuộc giải thưởng này!');
        }

        try {
            // Delete attachment file if exists
            if ($recipient->attachment && Storage::disk('public')->exists($recipient->attachment)) {
                Storage::disk('public')->delete($recipient->attachment);
            }

            $recipient->delete();

            return redirect()->back()->with('success', 'Người nhận đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
