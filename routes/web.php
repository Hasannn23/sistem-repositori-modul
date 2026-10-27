<?php

use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\KajurModuleController;
use App\Http\Controllers\ModuleController;
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

    // Resource routes for CourseClass and Module
    Route::resource('classes', CourseClassController::class);
    Route::resource('course-classes', CourseClassController::class);
    Route::post('/modules/{module}/submit', [ModuleController::class, 'submit'])->name('modules.submit');
    Route::post('/modules/{module}/update-revision', [ModuleController::class, 'updateRevision'])
        ->middleware('dosen')
        ->name('modules.updateRevision');
    Route::resource('modules', ModuleController::class);

    // Kajur Module Review & Approval Routes
    Route::get('/kajur/modules', [KajurModuleController::class, 'index'])->name('kajur.modules.index');
    Route::post('/kajur/modules/{module}/approve', [KajurModuleController::class, 'approve'])->name('kajur.modules.approve');
    Route::post('/kajur/modules/{module}/reject', [KajurModuleController::class, 'reject'])->name('kajur.modules.reject');
});

require __DIR__.'/auth.php';
