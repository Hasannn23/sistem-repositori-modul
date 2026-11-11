<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseClassController extends Controller
{
    /**
     * Display a listing of classes belonging to the logged-in lecturer (dosen).
     */
    public function index(): View
    {
        $user = Auth::user();

        $classes = CourseClass::where('dosen_id', $user->id)
            ->with(['course.masterModule', 'students'])
            ->withCount('students')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $courses = Course::query()
            ->when($user->department_id, function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->orderBy('name')
            ->get();

        $totalStudents = CourseClass::where('dosen_id', $user->id)
            ->withCount('students')
            ->get()
            ->sum('students_count');

        return view('dosen.classes.index', compact('classes', 'courses', 'totalStudents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('classes.index');
    }

    /**
     * Store a newly created course class in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        // Jika ada data courses di database atau input course_id dikirim, wajibkan course_id
        if ($request->has('course_id') || Course::exists()) {
            $rules['course_id'] = ['required', 'exists:courses,id'];
        } else {
            $rules['course_id'] = ['nullable', 'exists:courses,id'];
        }

        $validated = $request->validate($rules, [
            'course_id.required' => 'Mata kuliah wajib dipilih.',
            'course_id.exists' => 'Mata kuliah yang dipilih tidak valid.',
            'name.required' => 'Nama ruang kelas wajib diisi.',
            'name.max' => 'Nama ruang kelas maksimal 255 karakter.',
        ]);

        // Generate unique referral code (uppercase 6-character random string)
        do {
            $referralCode = Str::upper(Str::random(6));
        } while (CourseClass::where('referral_code', $referralCode)->exists());

        $courseClass = CourseClass::create([
            'course_id' => $validated['course_id'] ?? null,
            'dosen_id' => $user->id,
            'name' => $validated['name'],
            'referral_code' => $referralCode,
        ]);

        return redirect()->route('classes.index')->with('success', 'Ruang kelas baru berhasil dibuat!');
    }

    /**
     * Display the specified course class and its modules.
     */
    public function show(CourseClass $class): RedirectResponse
    {
        if ($class->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke ruang kelas ini.');
        }

        if ($class->course?->masterModule) {
            return redirect()->route('modules.lms', $class->course->masterModule->id);
        }

        return redirect()->route('classes.index')
            ->with('info', "Modul praktikum untuk mata kuliah \"{$class->course?->name}\" sedang dalam proses penyusunan.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseClass $class): RedirectResponse
    {
        return redirect()->route('classes.index');
    }

    /**
     * Update the specified course class in storage.
     */
    public function update(Request $request, CourseClass $class): RedirectResponse
    {
        if ($class->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah kelas ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama ruang kelas wajib diisi.',
            'name.max' => 'Nama ruang kelas maksimal 255 karakter.',
        ]);

        $class->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('classes.index')->with('success', 'Ruang kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified course class from storage.
     */
    public function destroy(CourseClass $class): RedirectResponse
    {
        if ($class->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus kelas ini.');
        }

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Ruang kelas berhasil dihapus.');
    }
}
