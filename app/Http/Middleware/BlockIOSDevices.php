<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIOSDevices
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra bật tắt qua env
        if (env('BLOCK_IOS_DEVICES', true)) {
            $userAgent = $request->header('User-Agent');

            // Nếu user-agent là iOS
            if (preg_match('/iPhone|iPad|iPod|Macintosh/i', $userAgent)) {
                // Nếu request là API thì trả JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Hệ thống hiện tại không khả dụng trên thiết bị iOS.'
                    ], 403);
                }

                // Trả về view error
                return response()->view('errors.block_ios', [], 403);
            }
        }

        return $next($request);
    }
}
