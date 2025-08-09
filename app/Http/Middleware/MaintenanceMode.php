<?php
// Created: Maintenance mode middleware with bypass token support

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MaintenanceMode
{
    private const BYPASS_TIMEOUT = 600; // 10 minutes in seconds
    private const BYPASS_COOKIE_TIMEOUT = 10; // 10 minutes for cookies

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if maintenance mode is enabled
        if (Setting::getValue(null, 'maintenance_mode') !== '1') {
            return $next($request);
        }

        $storedToken = Setting::getValue(null, 'maintenance_bypass_token');
        $path = $request->path();
        $currentTime = time();

        // Check for bypass token in URL path
        $bypassToken = $this->extractBypassTokenFromPath($path);

        // Allow access if bypass token is valid in URL
        if ($bypassToken && $storedToken && $bypassToken === $storedToken) {
            return $this->createBypassSession($request, $next, $storedToken, $currentTime);
        }

        // Special handling for bypass route
        if ($path === 'bypass/' . $storedToken && $storedToken) {
            return $this->createBypassSession($request, $next, $storedToken, $currentTime);
        }

        // Check if user has valid bypass session
        if ($this->hasValidBypassSession($request, $storedToken, $currentTime)) {
            return $next($request);
        }

        // Check if user is admin with maintenance permission
        if (Auth::check() && Auth::user()->hasPermissionForModuleCode('setting_management', 'maintenance', 'central')) {
            return $next($request);
        }

        // Return maintenance page
        return response()->view('errors.503', [], 503);
    }

    /**
     * Extract bypass token from URL path
     */
    private function extractBypassTokenFromPath(string $path): ?string
    {
        if (preg_match('/^bypass\/([a-zA-Z0-9_]+)$/', $path, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Check if user has valid bypass session
     */
    private function hasValidBypassSession(Request $request, ?string $storedToken, int $currentTime): bool
    {
        if (!$storedToken) {
            return false;
        }

        $bypassSessionKey = 'maintenance_bypass_' . $storedToken;

        // Check session
        $lastActivity = session($bypassSessionKey);
        if ($lastActivity && ($currentTime - $lastActivity) <= self::BYPASS_TIMEOUT) {
            session([$bypassSessionKey => $currentTime]);
            return true;
        }

        // Check cookie
        $bypassCookie = $request->cookie('maintenance_bypass_token');
        $bypassCookieTime = $request->cookie('maintenance_bypass_time');
        if ($bypassCookie === $storedToken && $bypassCookieTime && ($currentTime - $bypassCookieTime) <= self::BYPASS_TIMEOUT) {
            return true;
        }

        // Check cache
        $cacheKey = 'maintenance_bypass_' . $storedToken;
        $cacheTime = Cache::get($cacheKey);
        if ($cacheTime && ($currentTime - $cacheTime) <= self::BYPASS_TIMEOUT) {
            Cache::put($cacheKey, $currentTime, self::BYPASS_TIMEOUT);
            return true;
        }

        return false;
    }

    /**
     * Create bypass session and return response
     */
    private function createBypassSession(Request $request, Closure $next, string $storedToken, int $currentTime)
    {
        $bypassSessionKey = 'maintenance_bypass_' . $storedToken;
        $cacheKey = 'maintenance_bypass_' . $storedToken;

        // Set session
        session([$bypassSessionKey => $currentTime]);

        // Set cache
        Cache::put($cacheKey, $currentTime, self::BYPASS_TIMEOUT);

        // Set cookies
        $response = $next($request);
        $response->cookie('maintenance_bypass_token', $storedToken, self::BYPASS_COOKIE_TIMEOUT);
        $response->cookie('maintenance_bypass_time', $currentTime, self::BYPASS_COOKIE_TIMEOUT);

        return $response;
    }
}
