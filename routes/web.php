<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\LandingPage\Home;
use App\Livewire\LandingPage\About;
use App\Livewire\LandingPage\Team;
use App\Livewire\LandingPage\HowItWorks;
use App\Livewire\LandingPage\Testimonials;
use App\Livewire\LandingPage\NewsUpdates;
use App\Livewire\LandingPage\Contact;
use App\Models\Activation;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', Home::class)->name('home');

Route::get('/about', About::class)->name('about');

Route::get('/team', Team::class)->name('team');

Route::get('/how-it-works', HowItWorks::class)->name('how-it-works');

Route::get('/testimonials', Testimonials::class)->name('testimonials');

Route::get('/news', NewsUpdates::class)->name('news');

Route::get('/contact', Contact::class)->name('contact');

/*
|--------------------------------------------------------------------------
| Member Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        if (Auth::user()->RoleID == 1) {
            return view('pages.admin-pages.dashboard');
        }

    return view('pages.member-pages.dashboard');
    })->name('dashboard');

    Route::get('/genealogy-tree', function () {
        return view('pages.member-pages.genealogy-tree');
    })->name('genealogy-tree');

    Route::get('/masterlist', function () {
        return view('pages.admin-pages.masterlist');
    })->name('masterlist');

    Route::get('/post', function () {
        return view('pages.admin-pages.post');
    })->name('post');

    Route::get('/report-settings', function () {
        return view('pages.admin-pages.report-settings');
    })->name('report-settings');


    Route::get('/my-profile', function () {

    $currentUserId = Auth::id();

    $hasActivation = Activation::where('ProcessedBy', $currentUserId)
        ->whereIn('status', [
            'Pending',
            'Approved'
        ])
        ->exists();

    return view(
        'pages.member-pages.my-profile',
        compact('hasActivation')
    );

})->name('my-profile');

    Route::post('/logout', function () {

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('home');

    })->name('logout');

});



require __DIR__.'/settings.php';
