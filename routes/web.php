<?php

use App\Http\Controllers\ProfileController;
use App\Models\Module;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $modules = Module::with(['courseClass', 'courseClass.dosen'])
        ->where('status', 'approved')
        ->where('is_locked', false)
        ->latest()
        ->get();

    return view('welcome', compact('modules'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
