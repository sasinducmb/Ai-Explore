<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip timeout for admin users - check both email and role
            if ($user->email === 'admin@example.com' ||
                ($user->role ?? null) === 'ADMIN' ||
                ($user->is_admin ?? false)) {
                return $next($request);
            }

            $timeout = 30 * 60; // 30 minutes in seconds
            $lastActivity = Session::get('last_activity', time());

            // Check if session has timed out
            if (time() - $lastActivity > $timeout) {
                Session::flush();
                Auth::logout();

                if ($request->ajax()) {
                    return response()->json(['timeout' => true, 'message' => 'Your session has expired. Please log in again.'], 401);
                }

                return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
            }

            // Update last activity time
            Session::put('last_activity', time());
            Session::put('session_timeout', $timeout);
        }

        return $next($request);
    }
}
