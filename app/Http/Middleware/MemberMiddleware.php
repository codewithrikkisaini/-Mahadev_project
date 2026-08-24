<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your member account.');
        }

        if (Auth::user()->isAdmin()) {
            // Admin can also view or redirect to admin panel
            return redirect()->route('admin.dashboard');
        }

        if (!Auth::user()->isMember()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
