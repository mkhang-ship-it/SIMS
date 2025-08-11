<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DisciplineRecord;
use App\Models\Semester;
use App\Models\UserClubRole;
use App\Models\SchoolPoint;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use Illuminate\Support\Facades\DB;

class MyPointsController extends Controller
{
    /**
     * Display current user's discipline points
     */
    public function index(Request $request)
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

        // Check if semester exists, if not return error
        if (!$selectedSemester) {
            return redirect()->back()->with('error', 'Không tìm thấy học kỳ nào để xem điểm.');
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

        // Calculate bonus points (positive points)
        $bonusPoints = DisciplineRecord::active()
            ->byUser($user->id)
            ->bySemester($selectedSemester->id)
            ->byClub($clubId)
            ->join('disciplines', 'discipline_records.discipline_id', '=', 'disciplines.id')
            ->where('disciplines.discipline_points', '>', 0)
            ->sum('disciplines.discipline_points');

        // Calculate penalty points (negative points) - convert to positive for display
        $penaltyPoints = abs(DisciplineRecord::active()
            ->byUser($user->id)
            ->bySemester($selectedSemester->id)
            ->byClub($clubId)
            ->join('disciplines', 'discipline_records.discipline_id', '=', 'disciplines.id')
            ->where('disciplines.discipline_points', '<', 0)
            ->sum('disciplines.discipline_points'));

        // Get user's ranking in the club for this semester
        $ranking = $this->getUserRanking($user->id, $clubId, $selectedSemester->id);

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

        return view('clubs.discipline-records.view_my_point', compact(
            'user',
            'disciplineRecords',
            'totalClubPoints',
            'bonusPoints',
            'penaltyPoints',
            'ranking',
            'schoolPoint',
            'userClubRole',
            'selectedSemester',
            'semesters',
            'club'
        ));
    }

    /**
     * Get user's ranking in the club for a specific semester
     */
    private function getUserRanking($userId, $clubId, $semesterId)
    {
        // Get all club members with their total points for the semester
        $memberPoints = DisciplineRecord::active()
            ->bySemester($semesterId)
            ->byClub($clubId)
            ->join('disciplines', 'discipline_records.discipline_id', '=', 'disciplines.id')
            ->select('discipline_records.user_id', DB::raw('SUM(disciplines.discipline_points) as total_points'))
            ->groupBy('discipline_records.user_id')
            ->orderBy('total_points', 'desc')
            ->get();

        // Find current user's position
        $userPosition = $memberPoints->search(function ($member) use ($userId) {
            return $member->user_id === $userId;
        });

        // Return ranking (1-based index)
        if ($userPosition !== false) {
            return $userPosition + 1;
        }

        return '-';
    }
}
