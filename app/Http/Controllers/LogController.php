<?php

/**
 * Updated: Added pagination and caching to improve page load speed for 1000+ records
 * - Added pagination with 50 records per page
 * - Added cache for statistics (5 minutes)
 * - Added cache for users list (30 minutes)
 * - Improved performance for large datasets
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

        // Phân trang (50 bản ghi mỗi trang thay vì lấy tất cả)
        $perPage = $request->get('per_page', 10);
        $logs = $query->paginate($perPage)->appends($request->query());

        // Cache thống kê tổng quan (5 phút) - chỉ tính lại khi cần thiết
        $stats = Cache::remember('system_logs_stats', 300, function () {
            return [
                'totalLogs' => SystemLog::count(),
                'uniqueUsers' => SystemLog::distinct('user_id')->count(),
                'uniqueIPs' => SystemLog::distinct('ip')->count(),
            ];
        });

        // Cache thống kê hôm nay (1 phút) - cập nhật thường xuyên hơn
        $todayStats = Cache::remember('system_logs_today_stats', 60, function () {
            return [
                'todayLogs' => SystemLog::whereDate('created_at', Carbon::today())->count(),
            ];
        });

        // Cache thống kê theo method (10 phút)
        $methodStats = Cache::remember('system_logs_method_stats', 600, function () {
            return SystemLog::select('method', DB::raw('count(*) as count'))
                ->groupBy('method')
                ->orderBy('count', 'desc')
                ->get();
        });

        // Cache thống kê theo giờ trong ngày (5 phút)
        $hourlyStats = Cache::remember('system_logs_hourly_stats_' . Carbon::today()->format('Y-m-d'), 300, function () {
            return SystemLog::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
                ->whereDate('created_at', Carbon::today())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        });

        // Cache danh sách users (30 phút)
        $users = Cache::remember('system_logs_users_list', 1800, function () {
            return User::orderBy('name')->get(['id', 'name']);
        });

        // Danh sách methods để filter
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

        return view('logs.index', compact(
            'logs',
            'stats',
            'todayStats',
            'methodStats',
            'hourlyStats',
            'users',
            'methods'
        ));
    }

    public function destroy(SystemLog $log)
    {
        $log->delete();

        // Clear cache sau khi xóa
        $this->clearStatsCache();

        return back()->with('success', 'Đã xóa log thành công!');
    }

    public function destroyAll()
    {
        SystemLog::truncate();

        // Clear cache sau khi xóa tất cả
        $this->clearStatsCache();

        return back()->with('success', 'Đã xóa tất cả log thành công!');
    }

    public function destroyOld(Request $request)
    {
        $days = $request->input('days', 30);
        $deletedCount = SystemLog::where('created_at', '<', Carbon::now()->subDays($days))->delete();

        // Clear cache sau khi xóa log cũ
        $this->clearStatsCache();

        return back()->with('success', "Đã xóa {$deletedCount} log cũ hơn {$days} ngày!");
    }

    /**
     * Clear all statistics cache
     */
    private function clearStatsCache()
    {
        Cache::forget('system_logs_stats');
        Cache::forget('system_logs_today_stats');
        Cache::forget('system_logs_method_stats');
        Cache::forget('system_logs_hourly_stats_' . Carbon::today()->format('Y-m-d'));
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

        // Giới hạn số lượng export để tránh memory issues
        $maxExportLimit = 50000;
        $totalCount = $query->count();

        if ($totalCount > $maxExportLimit) {
            return back()->withErrors(['export' => "Chỉ có thể export tối đa {$maxExportLimit} bản ghi. Hiện tại có {$totalCount} bản ghi phù hợp. Vui lòng sử dụng bộ lọc để giảm số lượng."]);
        }

        $filename = 'system_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($query) {
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

            // Sử dụng chunk để xử lý theo batch, tránh memory issues
            $query->chunk(1000, function ($logs) use ($file) {
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
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
