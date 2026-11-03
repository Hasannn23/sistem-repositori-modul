<?php

use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\KajurModuleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\CourseClass;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $search = $request->query('search');

    $classes = CourseClass::whereHas('modules', function ($query) {
        $query->where('status', 'approved');
    })
        ->with('dosen')
        ->withCount(['modules' => function ($query) {
            $query->where('status', 'approved');
        }])
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('dosen', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('welcome', compact('classes', 'search'));
});

Route::get('/katalog/kelas/{courseClass}', function (CourseClass $courseClass, Request $request) {
    $courseClass->load(['dosen', 'dosen.department']);
    $search = $request->query('search');

    $modules = $courseClass->modules()
        ->where('status', 'approved')
        ->when($search, function ($query, $search) {
            $query->where('title', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('katalog.kelas', compact('courseClass', 'modules', 'search'));
})->name('katalog.class');

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'mahasiswa') {
        return redirect()->route('student.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mahasiswa / Student Routes
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/join', [StudentController::class, 'join'])->name('student.join');
    Route::get('/student/classes/{courseClass}', [StudentController::class, 'showClass'])->name('student.class.show');

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

    // Super Admin Master Data Routes
    Route::middleware('super_admin')->group(function () {
        Route::resource('faculties', FacultyController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
