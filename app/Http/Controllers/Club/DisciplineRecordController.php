<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DisciplineRecord;
use App\Models\Discipline;
use App\Models\User;
use App\Models\Semester;
use App\Models\UserClubRole;
use App\Models\SchoolPoint;
use Illuminate\Support\Facades\Auth;
use App\Exports\DisciplineRecordsSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Club;

class DisciplineRecordController extends Controller
{
    /**
     * Display a listing of discipline records for current club
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Base query
        $query = DisciplineRecord::with([
            'user',
            'discipline',
            'semester',
            'appliedBy'
        ])->byClub($clubId);

        // Filters
        if ($request->filled('semester_id')) {
            $query->bySemester($request->semester_id);
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline_id);
        }

        $disciplineRecords = $query->orderBy('applied_date', 'desc')->get();

        // Get filter options
        $semesters = Semester::where('status', 'active')->orderBy('start_date', 'desc')->get();
        $disciplines = Discipline::byClub($clubId)->where('status', 'active')->orderBy('name')->get();

        // Get club members for filtering
        $clubMembers = UserClubRole::with('user')
            ->byClub($clubId)
            ->where('status', 'active')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->sortBy('name');

        return view('clubs.discipline-records.index', compact(
            'disciplineRecords',
            'semesters',
            'disciplines',
            'clubMembers'
        ));
    }

    /**
     * Store a newly created discipline record
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'semester_id' => 'required|exists:semesters,id',
            'applied_date' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Validate that user belongs to this club
            $userInClub = UserClubRole::where('user_id', $validatedData['user_id'])
                ->where('club_id', $clubId)
                ->where('status', 'active')
                ->exists();

            if (!$userInClub) {
                return redirect()->back()->with('error', 'Thành viên không thuộc câu lạc bộ này!');
            }

            // Validate that discipline belongs to this club
            $discipline = Discipline::where('id', $validatedData['discipline_id'])
                ->where('club_id', $clubId)
                ->first();

            if (!$discipline) {
                return redirect()->back()->with('error', 'Loại kỷ luật không thuộc câu lạc bộ này!');
            }

            $recordData = [
                'user_id' => $validatedData['user_id'],
                'club_id' => $clubId,
                'discipline_id' => $validatedData['discipline_id'],
                'semester_id' => $validatedData['semester_id'],
                'applied_date' => $validatedData['applied_date'],
                'applied_by' => $user->id,
                'notes' => $validatedData['notes']
            ];

            DisciplineRecord::create($recordData);

            // Tự động tính lại điểm trường cho club và semester này
            SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $validatedData['semester_id']);

            return redirect()->back()->with('success', 'Đã ghi nhận vi phạm kỷ luật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified discipline record
     */
    public function update(Request $request, DisciplineRecord $disciplineRecord)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if record belongs to user's club
        if ($disciplineRecord->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa vi phạm này!');
        }

        $validatedData = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
            'semester_id' => 'required|exists:semesters,id',
            'applied_date' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Validate that discipline belongs to this club
            $discipline = Discipline::where('id', $validatedData['discipline_id'])
                ->where('club_id', $clubId)
                ->first();

            if (!$discipline) {
                return redirect()->back()->with('error', 'Loại kỷ luật không thuộc câu lạc bộ này!');
            }

            $disciplineRecord->update($validatedData);

            // Tự động tính lại điểm trường cho club và semester này
            SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $validatedData['semester_id']);

            return redirect()->back()->with('success', 'Đã cập nhật vi phạm kỷ luật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Revoke (thu hồi) the specified discipline record
     */
    public function revoke(DisciplineRecord $disciplineRecord)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if record belongs to user's club
        if ($disciplineRecord->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền thu hồi vi phạm này!');
        }

        try {
            $disciplineRecord->update([
                'status' => 'revoked',
                'notes' => ($disciplineRecord->notes ? $disciplineRecord->notes . "\n" : '') .
                    "Thu hồi bởi: " . $user->name . " vào " . now()->format('d/m/Y H:i')
            ]);

            // Tự động tính lại điểm trường cho club và semester này
            SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $disciplineRecord->semester_id);

            return redirect()->back()->with('success', 'Đã thu hồi vi phạm kỷ luật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified discipline record
     */
    public function destroy(DisciplineRecord $disciplineRecord)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if record belongs to user's club
        if ($disciplineRecord->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa vi phạm này!');
        }

        try {
            $semesterId = $disciplineRecord->semester_id; // Lưu lại trước khi xóa
            $disciplineRecord->delete();

            // Tự động tính lại điểm trường cho club và semester này
            SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $semesterId);

            return redirect()->back()->with('success', 'Đã xóa vi phạm kỷ luật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display discipline summary by members
     */
    public function summary(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get selected semester or default to current active semester
        $selectedSemester = null;
        if ($request->filled('semester_id')) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        if (!$selectedSemester) {
            $selectedSemester = Semester::where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->first();
        }

        // Get all club members with their discipline points
        $clubMembers = UserClubRole::with(['user', 'role'])
            ->byClub($clubId)
            ->where('status', 'active')
            ->get();

        // Tự động tính lại điểm trường cho toàn bộ club
        SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $selectedSemester->id);

        $membersSummary = [];
        foreach ($clubMembers as $member) {
            $totalClubPoints = DisciplineRecord::calculateTotalPoints(
                $member->user_id,
                $selectedSemester->id,
                $clubId
            );

            $schoolPoint = SchoolPoint::getSchoolPoint(
                $member->user_id,
                $clubId,
                $selectedSemester->id
            );

            $recentRecords = DisciplineRecord::with(['discipline'])
                ->byUser($member->user_id)
                ->bySemester($selectedSemester->id)
                ->byClub($clubId)
                ->active()
                ->orderBy('applied_date', 'desc')
                ->limit(3)
                ->get();

            $membersSummary[] = [
                'member' => $member,
                'total_points' => $totalClubPoints, // Giữ nguyên tên cho compatibility
                'club_points' => $totalClubPoints,
                'school_points' => $schoolPoint ? $schoolPoint->school_points : 0,
                'is_leader' => $schoolPoint ? $schoolPoint->is_leader : false,
                'recent_records' => $recentRecords
            ];
        }

        // Sort by total points descending
        usort($membersSummary, function ($a, $b) {
            return $b['total_points'] <=> $a['total_points'];
        });

        $semesters = Semester::where('status', 'active')->orderBy('start_date', 'desc')->get();

        return view('clubs.discipline-records.summary', compact(
            'membersSummary',
            'selectedSemester',
            'semesters'
        ));
    }

    /**
     * Export discipline records summary to Excel
     */
    public function exportSummary(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get selected semester or default to current active semester
        $selectedSemester = null;
        if ($request->filled('semester_id')) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        if (!$selectedSemester) {
            $selectedSemester = Semester::where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->first();
        }

        // Get club name for the export
        $club = Club::find($clubId);
        $clubName = $club ? $club->name : 'Câu lạc bộ';

        // Auto-calculate school points before export
        SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $selectedSemester->id);

        $fileName = 'Tổng Hợp Điểm Thành Viên - Học Kỳ ' . $selectedSemester->name . '.xlsx';

        return Excel::download(
            new DisciplineRecordsSummaryExport(
                $clubId,
                $selectedSemester->id,
                $selectedSemester->name,
                $clubName
            ),
            $fileName
        );
    }

    /**
     * Display current user's discipline points
     */
    public function myPoints(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get selected semester or default to current active semester
        $selectedSemester = null;
        if ($request->filled('semester_id')) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        if (!$selectedSemester) {
            $selectedSemester = Semester::where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->first();
        }

        // Auto-calculate school points for current user
        SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $selectedSemester->id);

        // Get user's discipline records for the selected semester
        $disciplineRecords = DisciplineRecord::with(['discipline', 'semester', 'appliedBy'])
            ->byUser($user->id)
            ->bySemester($selectedSemester->id)
            ->byClub($clubId)
            ->active()
            ->orderBy('applied_date', 'desc')
            ->get();

        // Calculate total points
        $totalClubPoints = DisciplineRecord::calculateTotalPoints(
            $user->id,
            $selectedSemester->id,
            $clubId
        );

        // Get school point information
        $schoolPoint = SchoolPoint::getSchoolPoint(
            $user->id,
            $clubId,
            $selectedSemester->id
        );

        // Get user's club role
        $userClubRole = UserClubRole::with('role')
            ->where('user_id', $user->id)
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->first();

        // Get all available semesters for filtering
        $semesters = Semester::where('status', 'active')->orderBy('start_date', 'desc')->get();

        // Get club information
        $club = Club::find($clubId);

        return view('clubs.discipline-records.my-points', compact(
            'disciplineRecords',
            'totalClubPoints',
            'schoolPoint',
            'userClubRole',
            'selectedSemester',
            'semesters',
            'club'
        ));
    }
}
