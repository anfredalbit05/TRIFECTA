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
use App\Livewire\LoginModal;
use App\Livewire\MemberPages\MyProfile\MyProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use App\Http\Controllers\MemberIdCardController;

/*
|--------------------------------------------------------------------------
| Public Pages (Protected by Maintenance Mode)
|--------------------------------------------------------------------------
*/

Route::middleware(['maintenance'])->group(function () {
    Route::get('/', Home::class)->name('home');
    Route::get('/about', About::class)->name('about');
    Route::get('/team', Team::class)->name('team');
    Route::get('/how-it-works', How::class)->name('how-it-works');
    Route::get('/testimonials', Testimonials::class)->name('testimonials');
    Route::get('/news', NewsUpdates::class)->name('news');
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/login', Home::class)->defaults('autoLogin', true)->name('secret.login');

});

Route::get('/register/{referralCode}', function ($referralCode) {
    return redirect()->route('home', ['ref' => $referralCode]);
})->name('register.referral');

/*
|--------------------------------------------------------------------------
| Member Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        // 1. Check if maintenance mode is enabled
        $maintenance = Setting::where('name', 'maintenance_mode')->value('value');

        // 2. If maintenance is active and the user is NOT a Super Admin (RoleID 1)
        if (($maintenance === '1' || $maintenance === 'true') && optional(Auth::user())->RoleID !== 1) {
            return response()->view('maintenance', [], 503);
        }
        // if(Auth::check()){
        //     return view('livewire.landing-page.home');
        // }

        return view('pages.share-pages.dashboard');
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

    Route::get('/policy/download', [PolicyController::class, 'download'])
        ->name('policy.download');

    Route::get('/my-profile', function () {
        $currentUserId = Auth::id();
        $activation = Activation::where('ProcessedBy', $currentUserId)
            ->latest()
            ->first();
        $activationStatus = $activation?->status;

        return view('pages.member-pages.my-profile', compact('activationStatus'));
    })->name('my-profile');

    Route::get('/member/id-card/pdf', [MemberIdCardController::class, 'download'])
        ->name('member.id-card.pdf');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});

Route::get('/secret-login', Home::class)->defaults('autoLogin', true)->name('secret.login');

require __DIR__ . '/settings.php';
