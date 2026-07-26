<?php

namespace App\Http\Controllers;

use App\Models\Context;

class HomeController extends Controller
{
    public function index()
    {
        // Load all editable content once
        $contexts = Context::pluck('details', 'context_key')->toArray();

        return view('pages.home', compact('contexts'));
    }
}
