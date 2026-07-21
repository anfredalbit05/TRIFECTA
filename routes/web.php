<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

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



require __DIR__.'/settings.php';
