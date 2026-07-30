<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\LandingPage\Home;
use App\Livewire\LandingPage\About;
use App\Livewire\LandingPage\Team;
use App\Livewire\LandingPage\How;
use App\Livewire\LandingPage\Testimonials;
use App\Livewire\LandingPage\NewsUpdates;
use App\Livewire\LandingPage\Contact;
use App\Models\Activation;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PolicyController;
use App\Livewire\MemberPages\MyProfile\MyProfile;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', Home::class)->name('home');

Route::get('/about', About::class)->name('about');

Route::get('/team', Team::class)->name('team');

Route::get('/how-it-works', How::class)->name('how-it-works');

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

         Route::get('/certificate/download', [CertificateController::class, 'download'])
        ->name('certificate.download');

        Route::get(
    '/policy/download',
    [PolicyController::class, 'download']
)->middleware('auth')
 ->name('policy.download');


Route::get('/my-profile', function () {

    $currentUserId = Auth::id();

    $activation = Activation::where('ProcessedBy', $currentUserId)
        ->latest()
        ->first();

    $activationStatus = $activation?->status;

    return view(
        'pages.member-pages.my-profile',
        compact('activationStatus')
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
