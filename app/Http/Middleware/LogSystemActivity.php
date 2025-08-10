<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class LogSystemActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Chỉ log khi không phải request từ artisan, queue hoặc debug bar
            if (!app()->runningInConsole()) {
                SystemLog::create([
                    'user_id'     => Auth::id(),
                    'method'      => $request->method(),
                    'url'         => $request->fullUrl(),
                    'ip'          => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                    'request_data' => json_encode($request->except(['password', 'password_confirmation']))
                ]);
            }
        } catch (\Exception $e) {
            // Tránh làm hỏng flow nếu log lỗi
        }

        return $response;
    }
}
