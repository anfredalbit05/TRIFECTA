<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.home');
})->name('home');


Route::get('/about', function () {
    return view('pages.about');
})->name('about');


Route::get('/team', function () {
    return view('pages.team');
})->name('team');


Route::get('/how-it-works', function () {
    return view('pages.how-it-works');
})->name('how-it-works');


Route::get('/testimonials', function () {
    return view('pages.testimonials');
})->name('testimonials');


Route::get('/news', function () {
    return view('pages.news');
})->name('news');


Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');



/*
|--------------------------------------------------------------------------
| Member Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('pages.member-pages.dashboard');
    })->name('dashboard');

    Route::get('/masterlist', function () {
        return view('pages.member-pages.masterlist');
    })->name('masterlist');

    Route::get('/post', function () {
        return view('pages.member-pages.post');
    })->name('post');

    Route::get('/report-settings', function () {
        return view('pages.member-pages.report-settings');
    })->name('report-settings');

    Route::post('/logout', function () {

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('home');

    })->name('logout');

});



require __DIR__.'/settings.php';
