<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php'; // NOSONAR
require __DIR__ . '/auth.php'; // NOSONAR

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::resource('attendances', \App\Http\Controllers\AttendanceController::class)
            ->only(['index', 'create', 'store']);
    });
