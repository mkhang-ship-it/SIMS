<?php
// Updated: Modified CheckPermission middleware to support optional role type parameter and return JSON responses for AJAX requests when permission denied

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $moduleCode, $action, $roleType = null)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermissionForModuleCode($moduleCode, $action, $roleType)) {
            // Check if request expects JSON (AJAX)
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Bạn không có quyền thực hiện hành động này.'
                ], 403);
            }
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }
        return $next($request);
    }
}
