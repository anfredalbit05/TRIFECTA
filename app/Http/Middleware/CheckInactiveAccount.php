<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInactiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow admins who used the secret route
        if (
            session('maintenance_bypass') &&
            Auth::check() &&
            Auth::user()->RoleID == 1
        ) {
            return $next($request);
        }

        $hasInactiveAdmin = User::where('RoleID', 1)
            ->where('status', 'Inactive')
            ->exists();

        if ($hasInactiveAdmin) {
            return response()->view('maintenance');
        }

        return $next($request);
    }
}
