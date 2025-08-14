<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Extend the current session
     */
    public function extendSession(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip for admin users
            if ($user->email === 'admin@admin.com' ||
                ($user->role ?? null) === 'ADMIN' ||
                ($user->is_admin ?? false)) {
                return response()->json(['success' => false, 'message' => 'Admin sessions do not expire']);
            }

            // Update server-side session
            Session::put('last_activity', time());

            // Regenerate session to extend it server-side
            Session::migrate(true);

            return response()->json([
                'success' => true,
                'message' => 'Session extended successfully',
                'server_time' => time(),
                'new_expiry' => time() + (30 * 60),
                'session_id' => session()->getId()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }

    /**
     * Update user activity timestamp
     */
    public function updateActivity(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip for admin users
            if ($user->email === 'admin@example.com' ||
                ($user->role ?? null) === 'ADMIN' ||
                ($user->is_admin ?? false)) {
                return response()->json(['success' => true, 'message' => 'Admin activity tracking skipped']);
            }

            // Update last activity time on server
            Session::put('last_activity', time());

            return response()->json([
                'success' => true,
                'timestamp' => time(),
                'session_id' => session()->getId()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }

    /**
     * Get current session status
     */
    public function getSessionStatus(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip for admin users
            if ($user->email === 'admin@admin.com' ||
                ($user->role ?? null) === 'ADMIN' ||
                ($user->is_admin ?? false)) {
                return response()->json([
                    'success' => true,
                    'is_admin' => true,
                    'message' => 'Admin session - no timeout'
                ]);
            }

            $lastActivity = Session::get('last_activity', time());
            $timeout = 30 * 60; // 30 minutes
            $remaining = $timeout - (time() - $lastActivity);

            return response()->json([
                'success' => true,
                'remaining_seconds' => max(0, $remaining),
                'last_activity' => $lastActivity,
                'will_expire_at' => $lastActivity + $timeout,
                'session_id' => session()->getId(),
                'server_time' => time()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }
}
