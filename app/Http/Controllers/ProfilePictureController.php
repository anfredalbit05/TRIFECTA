<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePictureController extends Controller
{
    public function show()
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();

        $profilepic = $user->profilepic;

        // Default avatar
        if (empty($profilepic)) {
            $default = public_path('images/profiles/default-avatar.png');

            abort_unless(is_file($default), 404);

            return response()->file($default);
        }

        // Get the actual image path from Laravel storage
        $path = Storage::disk('public')->path($profilepic);

        if (!is_file($path)) {
            $default = public_path('images/profiles/default-avatar.png');

            abort_unless(is_file($default), 404);

            return response()->file($default);
        }

        return response()->file($path, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
