<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Event;
use App\Models\Course;
use App\Models\Semester;
use App\Models\ClassRoom;
use App\Models\Major;
use App\Models\Role;
use App\Models\Quote;
use App\Models\SystemLog;
use App\Models\Notification;
use App\Models\FeatureRequest;
use App\Models\UserClubRole;
use App\Models\EventCheckin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Overview Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_clubs' => Club::count(),
            'active_clubs' => Club::where('status', 'active')->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
            'pending_events' => Event::where('status', 'pending')->count(),
            'total_courses' => Course::count(),
            'total_semesters' => Semester::count(),
            'total_classrooms' => ClassRoom::count(),
            'total_majors' => Major::count(),
            'total_roles' => Role::where('type', 'central')->count(),
            'total_quotes' => Quote::count(),
            'total_notifications' => Notification::count(),
            'feature_requests' => FeatureRequest::count(),
            'pending_feature_requests' => FeatureRequest::where('status', 'pending')->count(),
        ];

        // User Growth (Last 12 months)
        $userGrowth = User::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Club Growth (Last 12 months)
        $clubGrowth = Club::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Event Statistics by Status
        $eventsByStatus = Event::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // User Distribution by Major
        $usersByMajor = User::join('majors', 'users.major_id', '=', 'majors.id')
            ->select('majors.name', DB::raw('COUNT(users.id) as count'))
            ->groupBy('majors.id', 'majors.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Club Members Distribution
        $clubMembersCount = Club::withCount('users')
            ->orderBy('users_count', 'desc')
            ->limit(10)
            ->get();

        // Recent Activities (Last 7 days)
        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentClubs = Club::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentEvents = Event::with(['club', 'semester'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // System Logs Statistics
        $systemStats = [
            'total_logs' => SystemLog::count(),
            'logs_today' => SystemLog::whereDate('created_at', Carbon::today())->count(),
            'logs_this_week' => SystemLog::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];

        // Top Activities (Most common URLs)
        $topActivities = SystemLog::select('url', DB::raw('COUNT(*) as count'))
            ->groupBy('url')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Event Check-in Statistics (if EventCheckin model exists)
        $eventCheckins = [];
        if (class_exists(EventCheckin::class)) {
            $eventCheckins = Event::withCount('checkins')
                ->where('status', 'active')
                ->orderBy('checkins_count', 'desc')
                ->limit(5)
                ->get();
        }

        // Monthly Event Distribution (Current Year)
        $monthlyEvents = Event::select(DB::raw('MONTH(event_date) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('event_date', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(event_date)'))
            ->orderBy('month')
            ->get();

        // User Registration Trend (Last 30 days)
        $userRegistrationTrend = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Feature Request Status Distribution
        $featureRequestStats = FeatureRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Most Active Clubs (by events)
        $mostActiveClubs = Club::withCount('events')
            ->orderBy('events_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'userGrowth',
            'clubGrowth',
            'eventsByStatus',
            'usersByMajor',
            'clubMembersCount',
            'recentUsers',
            'recentClubs',
            'recentEvents',
            'systemStats',
            'topActivities',
            'eventCheckins',
            'monthlyEvents',
            'userRegistrationTrend',
            'featureRequestStats',
            'mostActiveClubs'
        ));
    }
}
