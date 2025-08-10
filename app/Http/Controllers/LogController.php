<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemLog;
use App\Models\User;
use Carbon\Carbon;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with('user')
            ->orderBy('created_at', 'desc');

        // Lọc theo user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Lọc theo IP
        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%' . $request->ip . '%');
        }

        // Lọc theo URL
        if ($request->filled('url')) {
            $query->where('url', 'like', '%' . $request->url . '%');
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Phân trang
        $logs = $query->get();

        // Thống kê
        $totalLogs = SystemLog::count();
        $todayLogs = SystemLog::whereDate('created_at', Carbon::today())->count();
        $uniqueUsers = SystemLog::distinct('user_id')->count();
        $uniqueIPs = SystemLog::distinct('ip')->count();

        // Thống kê theo method
        $methodStats = SystemLog::select('method', DB::raw('count(*) as count'))
            ->groupBy('method')
            ->orderBy('count', 'desc')
            ->get();

        // Thống kê theo giờ trong ngày
        $hourlyStats = SystemLog::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Danh sách users để filter
        $users = User::orderBy('name')->get();

        // Danh sách methods để filter
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

        return view('logs.index', compact(
            'logs',
            'totalLogs',
            'todayLogs',
            'uniqueUsers',
            'uniqueIPs',
            'methodStats',
            'hourlyStats',
            'users',
            'methods'
        ));
    }



    public function destroy(SystemLog $log)
    {
        $log->delete();
        return back()->with('success', 'Đã xóa log thành công!');
    }

    public function destroyAll()
    {
        SystemLog::truncate();
        return back()->with('success', 'Đã xóa tất cả log thành công!');
    }

    public function destroyOld(Request $request)
    {
        $days = $request->input('days', 30);
        $deletedCount = SystemLog::where('created_at', '<', Carbon::now()->subDays($days))->delete();

        return back()->with('success', "Đã xóa {$deletedCount} log cũ hơn {$days} ngày!");
    }

    public function export(Request $request)
    {
        $query = SystemLog::with('user')
            ->orderBy('created_at', 'desc');

        // Áp dụng các filter tương tự như index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%' . $request->ip . '%');
        }
        if ($request->filled('url')) {
            $query->where('url', 'like', '%' . $request->url . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'system_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID',
                'User',
                'Method',
                'URL',
                'IP',
                'User Agent',
                'Request Data',
                'Created At'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'Guest',
                    $log->method,
                    $log->url,
                    $log->ip,
                    $log->user_agent,
                    $log->request_data,
                    $log->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
