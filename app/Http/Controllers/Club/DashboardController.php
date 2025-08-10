<?php
// Updated: Created comprehensive club dashboard with visual reports and statistics
// - Added statistics for club modules: members, events, awards, finances
// - Added visual charts data for ApexCharts integration
// - Added recent activities tracking and club-specific metrics
// - Added growth metrics and financial analysis for club management
// - All data is filtered by current user's default club

namespace App\Http\Controllers\Club;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Award;
use App\Models\UserClubRole;
use App\Models\LuckyDraw;
use App\Models\FeatureRequest;
use App\Models\SystemLog;
use App\Models\EventCheckin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * [AI CHANGE 2024-07-14] Lấy club_id từ session (theo SwitchClubController)
     * - Sử dụng $clubId = $request->session()->get('club_id');
     * - Nếu không có club_id trong session thì báo lỗi
     */
    public function index(Request $request)
    {
        $clubId = $request->session()->get('club_id');

        if (!$clubId) {
            return redirect()->back()->with('error', 'Bạn không thuộc bất kỳ câu lạc bộ nào.');
        }

        // Overview Statistics for current club
        $stats = [
            'total_members' => UserClubRole::where('club_id', $clubId)->where('status', 'active')->count(),
            'total_events' => Event::where('club_id', $clubId)->count(),
            'active_events' => Event::where('club_id', $clubId)->where('status', 'active')->count(),
            'pending_events' => Event::where('club_id', $clubId)->where('status', 'pending')->count(),
            'completed_events' => Event::where('club_id', $clubId)->where('status', 'completed')->count(),
            'total_awards' => Award::where('club_id', $clubId)->count(),
            'total_invoices' => Invoice::where('club_id', $clubId)->count(),
            'paid_invoices' => Invoice::where('club_id', $clubId)->where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('club_id', $clubId)->where('status', 'pending')->count(),
            'total_expenses' => Expense::where('club_id', $clubId)->count(),
            'approved_expenses' => Expense::where('club_id', $clubId)->where('status', 'approved')->count(),
            'pending_expenses' => Expense::where('club_id', $clubId)->where('status', 'pending')->count(),
            'total_lucky_draws' => LuckyDraw::where('club_id', $clubId)->count(),
            'active_lucky_draws' => LuckyDraw::where('club_id', $clubId)->where('is_active', true)->count(),
        ];

        // Financial Statistics
        $totalRevenue = Invoice::where('club_id', $clubId)->where('status', 'paid')->sum('total_amount');
        $totalExpenses = Expense::where('club_id', $clubId)->where('status', 'approved')->sum('amount');
        $pendingRevenue = Invoice::where('club_id', $clubId)->where('status', 'pending')->sum('total_amount');
        $pendingExpenses = Expense::where('club_id', $clubId)->where('status', 'pending')->sum('amount');

        $stats['total_revenue'] = $totalRevenue;
        $stats['total_expenses'] = $totalExpenses;
        $stats['net_profit'] = $totalRevenue - $totalExpenses;
        $stats['pending_revenue'] = $pendingRevenue;
        $stats['pending_expenses'] = $pendingExpenses;

        // User participated events statistics
        $participatedEvents = EventCheckin::where('event_checkins.user_id', Auth::user()->id)
            ->join('events', 'event_checkins.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->count();
        $stats['participated_events'] = $participatedEvents;

        // Event Growth (Last 12 months)
        $eventGrowth = Event::where('club_id', $clubId)
            ->select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Revenue Growth (Last 12 months)
        $revenueGrowth = Invoice::where('club_id', $clubId)
            ->where('status', 'paid')
            ->select(DB::raw('SUM(total_amount) as total'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Expense Growth (Last 12 months)
        $expenseGrowth = Expense::where('club_id', $clubId)
            ->where('status', 'approved')
            ->select(DB::raw('SUM(amount) as total'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Event Statistics by Status
        $eventsByStatus = Event::where('club_id', $clubId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Member Roles Distribution
        $membersByRole = UserClubRole::where('user_club_roles.club_id', $clubId)
            ->join('roles', 'user_club_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(user_club_roles.id) as count'))
            ->where('user_club_roles.status', 'active')
            ->groupBy('roles.id', 'roles.name')
            ->get();

        // Recent Activities (Last 7 days)
        $recentEvents = Event::where('club_id', $clubId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::where('club_id', $clubId)
            ->with('user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::where('club_id', $clubId)
            ->with(['requestedBy'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentMembers = UserClubRole::where('club_id', $clubId)
            ->with(['user', 'role'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent participated events by current user
        $recentParticipatedEvents = EventCheckin::where('event_checkins.user_id', Auth::user()->id)
            ->join('events', 'event_checkins.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->select('events.*', 'event_checkins.checkin_time', 'event_checkins.created_at as checkin_created_at')
            ->orderBy('event_checkins.checkin_time', 'desc')
            ->limit(5)
            ->get();

        // My event participation stats (current year)
        $myEventStats = EventCheckin::where('event_checkins.user_id', Auth::user()->id)
            ->join('events', 'event_checkins.event_id', '=', 'events.id')
            ->where('events.club_id', $clubId)
            ->whereYear('event_checkins.checkin_time', Carbon::now()->year)
            ->select(DB::raw('MONTH(event_checkins.checkin_time) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('MONTH(event_checkins.checkin_time)'))
            ->orderBy('month')
            ->get();

        // Monthly Event Distribution (Current Year)
        $monthlyEvents = Event::where('club_id', $clubId)
            ->select(DB::raw('MONTH(event_date) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('event_date', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(event_date)'))
            ->orderBy('month')
            ->get();

        // Monthly Revenue/Expense Comparison (Current Year)
        $monthlyRevenue = Invoice::where('club_id', $clubId)
            ->where('status', 'paid')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total'))
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        $monthlyExpense = Expense::where('club_id', $clubId)
            ->where('status', 'approved')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // Top Events by Check-ins (if EventCheckin model exists)
        $topEvents = [];
        if (class_exists(EventCheckin::class)) {
            $topEvents = Event::where('club_id', $clubId)
                ->withCount('checkins')
                ->orderBy('checkins_count', 'desc')
                ->limit(5)
                ->get();
        }

        // System Activities for this club (Last 30 days)
        $clubActivities = SystemLog::join('users', 'system_logs.user_id', '=', 'users.id')
            ->join('user_club_roles', 'users.id', '=', 'user_club_roles.user_id')
            ->where('user_club_roles.club_id', $clubId)
            ->where('system_logs.created_at', '>=', Carbon::now()->subDays(30))
            ->select('system_logs.url', DB::raw('COUNT(*) as count'))
            ->groupBy('system_logs.url')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Invoice Status Distribution
        $invoiceStats = Invoice::where('club_id', $clubId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Expense Status Distribution
        $expenseStats = Expense::where('club_id', $clubId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('clubs.dashboard.index', compact(
            'stats',
            'eventGrowth',
            'revenueGrowth',
            'expenseGrowth',
            'eventsByStatus',
            'membersByRole',
            'recentEvents',
            'recentInvoices',
            'recentExpenses',
            'recentMembers',
            'recentParticipatedEvents',
            'myEventStats',
            'monthlyEvents',
            'monthlyRevenue',
            'monthlyExpense',
            'topEvents',
            'clubActivities',
            'invoiceStats',
            'expenseStats'
        ));
    }
}
