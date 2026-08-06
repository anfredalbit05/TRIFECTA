<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Always allow Livewire requests and your secret login path
        if ($request->is('livewire/*') || $request->is('secret-login')) {
            return $next($request);
        }

        // Check if maintenance mode setting is enabled
        $maintenance = Setting::where('name', 'maintenance_mode')->value('value');

        if ($maintenance === '1' || $maintenance === 'true') {
            // Allow access if user is a Super Admin (RoleID 1)
            if (Auth::check() && (int) Auth::user()->RoleID === 1) {
                return $next($request);
            }

            // Block everyone else with the maintenance view
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
