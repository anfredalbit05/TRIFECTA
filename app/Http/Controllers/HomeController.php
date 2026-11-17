<?php

namespace App\Http\Controllers;

use App\Models\Context;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Optional: Check maintenance mode directly here if middleware isn't used globally
        $maintenance = Setting::where('name', 'maintenance_mode')->value('value');
        if ($maintenance === '1' || $maintenance === 'true') {
            if (!Auth::check() || (int) Auth::user()->RoleID !== 1) {
                return view('errors.maintenance');
            }
        }

        // Load all editable content once
        $contexts = Context::merged();

        return view('pages.home', compact('contexts'));
    }
}
