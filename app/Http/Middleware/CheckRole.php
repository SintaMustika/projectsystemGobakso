<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(['auth:sanctum','role:admin,owner'])
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $allowed = in_array($user->role ?? '', $roles, true);
        if (! $allowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            // Redirect web users to a safe page based on their role to avoid redirect loops
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('error', 'Unauthorized');
                case 'kasir':
                    return redirect()->route('pos.index')->with('error', 'Unauthorized');
                case 'dapur':
                    return redirect()->route('dapur.index')->with('error', 'Unauthorized');
                case 'owner':
                    return redirect()->route('admin.dashboard')->with('error', 'Unauthorized');
                default:
                    // Unknown role: logout and redirect to login to prevent loops
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Role not recognized');
            }
        }

        return $next($request);
    }
}
