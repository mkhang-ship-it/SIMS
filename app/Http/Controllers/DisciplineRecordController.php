<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisciplineRecord;
use App\Models\Discipline;
use App\Models\User;
use App\Models\Club;
use App\Models\Semester;
use App\Models\UserClubRole;
use App\Models\SchoolPoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\CentralDisciplineRecordsSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class DisciplineRecordController extends Controller
{
    /**
     * Display a listing of discipline records for all clubs (Central)
     */
    public function index(Request $request)
    {
        // Base query - get all discipline records across all clubs
        $query = DisciplineRecord::with([
            'user',
            'discipline',
            'semester',
            'appliedBy',
            'club'
        ]);

        // Filters
        if ($request->filled('club_id')) {
            $query->byClub($request->club_id);
        }

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
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        $semesters = Semester::where('status', 'active')->orderBy('start_date', 'desc')->get();

        // Get disciplines based on selected club (if any)
        $disciplines = collect();
        if ($request->filled('club_id')) {
            $disciplines = Discipline::byClub($request->club_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        // Get all users for filtering (could be optimized to show only club members)
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('discipline-records.index', compact(
            'disciplineRecords',
            'clubs',
            'semesters',
            'disciplines',
            'users'
        ));
    }

    /**
     * Display discipline records summary for all clubs or specific club (Central)
     */
    public function summary(Request $request)
    {
        // Get selected club (optional)
        $selectedClub = null;
        if ($request->filled('club_id')) {
            $selectedClub = Club::find($request->club_id);
        }

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

        // Check if semester exists, if not return error
        if (!$selectedSemester) {
            return redirect()->back()->with('error', 'Không tìm thấy học kỳ nào để xem tổng hợp.');
        }

        $membersSummary = [];

        if ($selectedClub) {
            // Show summary for specific club
            $clubMembers = UserClubRole::with(['user', 'role', 'club'])
                ->byClub($selectedClub->id)
                ->where('status', 'active')
                ->get();

            // Auto-calculate school points for the specific club
            SchoolPoint::calculateAndUpdateSchoolPoints($selectedClub->id, $selectedSemester->id);

            foreach ($clubMembers as $member) {
                $totalClubPoints = DisciplineRecord::calculateTotalPoints(
                    $member->user_id,
                    $selectedSemester->id,
                    $selectedClub->id
                );

                $schoolPoint = SchoolPoint::getSchoolPoint(
                    $member->user_id,
                    $selectedClub->id,
                    $selectedSemester->id
                );

                $recentRecords = DisciplineRecord::with(['discipline'])
                    ->byUser($member->user_id)
                    ->bySemester($selectedSemester->id)
                    ->byClub($selectedClub->id)
                    ->active()
                    ->orderBy('applied_date', 'desc')
                    ->limit(3)
                    ->get();

                $membersSummary[] = [
                    'member' => $member,
                    'total_points' => $totalClubPoints,
                    'club_points' => $totalClubPoints,
                    'school_points' => $schoolPoint ? $schoolPoint->school_points : 0,
                    'is_leader' => $schoolPoint ? $schoolPoint->is_leader : false,
                    'recent_records' => $recentRecords
                ];
            }
        } else {
            // Show summary for all clubs
            $allClubMembers = UserClubRole::with(['user', 'role', 'club'])
                ->where('status', 'active')
                ->get();

            // Group by club for batch processing
            $membersByClub = $allClubMembers->groupBy('club_id');

            foreach ($membersByClub as $clubId => $clubMembers) {
                // Auto-calculate school points for each club
                SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $selectedSemester->id);

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
                        'total_points' => $totalClubPoints,
                        'club_points' => $totalClubPoints,
                        'school_points' => $schoolPoint ? $schoolPoint->school_points : 0,
                        'is_leader' => $schoolPoint ? $schoolPoint->is_leader : false,
                        'recent_records' => $recentRecords
                    ];
                }
            }
        }

        // Sort by total points descending (highest positive points first)
        usort($membersSummary, function ($a, $b) {
            return $b['total_points'] <=> $a['total_points'];
        });

        // Get filter options
        $clubs = Club::where('status', 'active')->orderBy('name')->get();
        $semesters = Semester::where('status', 'active')->orderBy('start_date', 'desc')->get();

        return view('discipline-records.summary', compact(
            'membersSummary',
            'selectedSemester',
            'selectedClub',
            'clubs',
            'semesters'
        ));
    }

    /**
     * Export discipline records summary to Excel (Central)
     */
    public function exportSummary(Request $request)
    {
        // Get selected club (optional)
        $selectedClub = null;
        if ($request->filled('club_id')) {
            $selectedClub = Club::find($request->club_id);
        }

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

        // Check if semester exists, if not return error
        if (!$selectedSemester) {
            return redirect()->back()->with('error', 'Không tìm thấy học kỳ nào để xuất dữ liệu.');
        }

        if ($selectedClub) {
            // Auto-calculate school points for the specific club
            SchoolPoint::calculateAndUpdateSchoolPoints($selectedClub->id, $selectedSemester->id);
            $clubName = $selectedClub->name;
            $fileName = 'Tổng Hợp Điểm Thành Viên - ' . $clubName . ' - Học Kỳ ' . $selectedSemester->name . '.xlsx';

            return Excel::download(
                new CentralDisciplineRecordsSummaryExport(
                    $selectedClub->id,
                    $selectedSemester->id,
                    $selectedSemester->name,
                    $clubName
                ),
                $fileName
            );
        } else {
            // Export for all clubs
            $clubName = 'Tất Cả Câu Lạc Bộ';
            $fileName = 'Tổng Hợp Điểm Thành Viên - Học Kỳ ' . $selectedSemester->name . '.xlsx';

            return Excel::download(
                new CentralDisciplineRecordsSummaryExport(
                    null, // null means all clubs
                    $selectedSemester->id,
                    $selectedSemester->name,
                    $clubName
                ),
                $fileName
            );
        }
    }
}
