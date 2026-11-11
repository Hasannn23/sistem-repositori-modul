<?php

use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\Kajur\CourseController as KajurCourseController;
use App\Http\Controllers\KajurModuleController;
use App\Http\Controllers\LmsBuilderController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SayembaraController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Module;
use App\Models\Sayembara;
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
    $user = auth()->user();
    if ($user->role === 'mahasiswa') {
        return redirect()->route('student.dashboard');
    }

    $openSayembaras = collect();
    $stats = [];

    if ($user->role === 'dosen') {
        $openSayembaras = Sayembara::with(['course', 'modules.author', 'modules.courseClass.dosen'])
            ->when($user->department_id, function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->where('status', 'open')
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();

        $dosenClasses = CourseClass::with(['course.masterModule'])
            ->withCount('students')
            ->where('dosen_id', $user->id)
            ->latest()
            ->get();

        $totalClasses = $dosenClasses->count();
        $totalStudents = $dosenClasses->sum('students_count');

        $myModules = Module::where('author_id', $user->id)->get();
        $totalModules = $myModules->count();
        $draftModulesCount = $myModules->whereIn('status', ['draft', 'revision'])->count();
        $approvedModulesCount = $myModules->where('status', 'approved')->count();

        $draftModules = Module::with(['course', 'sayembara.course'])
            ->where('author_id', $user->id)
            ->whereIn('status', ['draft', 'revision'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'total_modules' => $totalModules,
            'draft_modules_count' => $draftModulesCount,
            'approved_modules_count' => $approvedModulesCount,
            'open_sayembaras_count' => $openSayembaras->count(),
            'recent_classes' => $dosenClasses->take(3),
            'draft_modules' => $draftModules,
        ];
    } elseif ($user->role === 'kajur') {
        $departmentId = $user->department_id;

        $coursesQuery = Course::query();
        if ($departmentId) {
            $coursesQuery->where('department_id', $departmentId);
        }
        $totalCourses = $coursesQuery->count();

        $waitingReviewsQuery = Module::with(['author', 'sayembara.course', 'course'])
            ->where('status', 'waiting_review');
        if ($departmentId) {
            $waitingReviewsQuery->where(function ($q) use ($departmentId) {
                $q->whereHas('course', fn($cq) => $cq->where('department_id', $departmentId))
                  ->orWhereHas('author', fn($aq) => $aq->where('department_id', $departmentId));
            });
        }
        $waitingReviews = $waitingReviewsQuery->latest()->get();

        $approvedModulesQuery = Module::where('status', 'approved');
        if ($departmentId) {
            $approvedModulesQuery->where(function ($q) use ($departmentId) {
                $q->whereHas('course', fn($cq) => $cq->where('department_id', $departmentId))
                  ->orWhereHas('author', fn($aq) => $aq->where('department_id', $departmentId));
            });
        }
        $approvedModulesCount = $approvedModulesQuery->count();

        $activeSayembarasQuery = Sayembara::where('status', 'open');
        if ($departmentId) {
            $activeSayembarasQuery->where('department_id', $departmentId);
        }
        $activeSayembarasCount = $activeSayembarasQuery->count();

        $stats = [
            'total_courses' => $totalCourses,
            'waiting_reviews_count' => $waitingReviews->count(),
            'approved_modules_count' => $approvedModulesCount,
            'active_sayembaras_count' => $activeSayembarasCount,
            'recent_waiting_reviews' => $waitingReviews->take(5),
        ];
    }

    return view('dashboard', compact('openSayembaras', 'stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mahasiswa / Student Routes
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/join', [StudentController::class, 'join'])->name('student.join');
    Route::get('/student/classes/{courseClass}', [StudentController::class, 'showClass'])->name('student.class.show');

    // Sayembara Pengusulan Modul Routes
    Route::get('/sayembaras', [SayembaraController::class, 'index'])->name('sayembaras.index');
    Route::get('/sayembaras/create', [SayembaraController::class, 'create'])->name('sayembaras.create');
    Route::post('/sayembaras', [SayembaraController::class, 'store'])->name('sayembaras.store');
    Route::get('/sayembaras/{sayembara}', [SayembaraController::class, 'show'])->name('sayembaras.show');
    Route::get('/sayembaras/{sayembara}/ikuti', [SayembaraController::class, 'showParticipateForm'])->name('dosen.sayembara.ikuti');
    Route::post('/sayembaras/{sayembara}/participate', [SayembaraController::class, 'participate'])->name('sayembaras.participate');
    Route::post('/sayembaras/{sayembara}/modules/{module}/approve-master', [SayembaraController::class, 'approveMaster'])->name('sayembaras.approve-master');
    Route::post('/sayembaras/{sayembara}/close', [SayembaraController::class, 'close'])->name('sayembaras.close');

    // LMS Module Builder Routes (Distraction-Free Workspace)
    Route::get('/modules/{module}/builder', [LmsBuilderController::class, 'edit'])->name('modules.builder');
    Route::get('/dosen/modules/{module}/builder', [LmsBuilderController::class, 'edit'])->name('dosen.modules.builder');
    Route::post('/modules/{module}/builder/autosave', [LmsBuilderController::class, 'autosave'])->name('modules.builder.autosave');
    Route::post('/modules/{module}/builder/meetings', [LmsBuilderController::class, 'storeMeeting'])->name('modules.builder.meetings.store');
    Route::put('/builder/meetings/{meeting}', [LmsBuilderController::class, 'updateMeeting'])->name('modules.builder.meetings.update');
    Route::delete('/builder/meetings/{meeting}', [LmsBuilderController::class, 'destroyMeeting'])->name('modules.builder.meetings.destroy');
    Route::post('/builder/meetings/{meeting}/items', [LmsBuilderController::class, 'storeItem'])->name('modules.builder.items.store');
    Route::delete('/builder/items/{item}', [LmsBuilderController::class, 'destroyItem'])->name('modules.builder.items.destroy');
    Route::post('/modules/{module}/builder/submit', [LmsBuilderController::class, 'submit'])->name('modules.builder.submit');

    // Resource routes for CourseClass and Module
    Route::resource('classes', CourseClassController::class);
    Route::resource('course-classes', CourseClassController::class);
    Route::get('/modules/{module}/lms', [ModuleController::class, 'showLms'])->name('modules.lms');
    Route::post('/modules/{module}/submit', [ModuleController::class, 'submit'])->name('modules.submit');
    Route::post('/modules/{module}/update-revision', [ModuleController::class, 'updateRevision'])
        ->middleware('dosen')
        ->name('modules.updateRevision');
    Route::resource('modules', ModuleController::class);

    // Kajur Module Review & Approval Routes
    Route::get('/kajur/modules', [KajurModuleController::class, 'index'])->name('kajur.modules.index');
    Route::get('/kajur/modules/{module}/review', [KajurModuleController::class, 'review'])->name('kajur.modules.review');
    Route::post('/kajur/modules/{module}/approve', [KajurModuleController::class, 'approve'])->name('kajur.modules.approve');
    Route::post('/kajur/modules/{module}/reject', [KajurModuleController::class, 'reject'])->name('kajur.modules.reject');

    // Kajur Course (Mata Kuliah) Management Routes
    Route::resource('kajur/courses', KajurCourseController::class)->names('kajur.courses');

    // Super Admin Master Data Routes
    Route::middleware('super_admin')->group(function () {
        Route::resource('faculties', FacultyController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
