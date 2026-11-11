<?php

namespace App\Http\Controllers\Kajur;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Authorize that the current authenticated user is a Kajur (or Super Admin)
     * and retrieve their department.
     */
    protected function authorizeKajur(): User
    {
        $user = Auth::user();

        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        if ($user->role === 'kajur' && !$user->department_id) {
            abort(403, 'Akun Kepala Jurusan Anda belum terhubung dengan Jurusan manapun.');
        }

        return $user;
    }

    /**
     * Display a listing of the courses for the Kajur's department.
     */
    public function index(Request $request): View
    {
        $user = $this->authorizeKajur();
        $departmentId = $user->department_id;
        $search = $request->query('search');

        $query = Course::query()
            ->with('department')
            ->withCount(['classes', 'modules']);

        // Scope to Kajur's department
        if ($user->role === 'kajur' && $departmentId) {
            $query->where('department_id', $departmentId);
        } elseif ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('code')->paginate(10)->withQueryString();

        // Department info for the view
        $department = $departmentId ? Department::find($departmentId) : null;
        $totalCourses = Course::when($departmentId && $user->role === 'kajur', fn ($q) => $q->where('department_id', $departmentId))->count();
        $totalCredits = Course::when($departmentId && $user->role === 'kajur', fn ($q) => $q->where('department_id', $departmentId))->sum('credit_hours');

        return view('kajur.courses.index', compact('courses', 'department', 'search', 'totalCourses', 'totalCredits'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        $user = $this->authorizeKajur();
        $department = $user->department ?? ($user->department_id ? Department::find($user->department_id) : null);

        return view('kajur.courses.create', compact('department'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->authorizeKajur();

        $departmentId = $user->department_id;
        if ($user->role === 'super_admin' && $request->filled('department_id')) {
            $departmentId = $request->input('department_id');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'code.required' => 'Kode mata kuliah wajib diisi.',
            'code.unique' => 'Kode mata kuliah sudah terdaftar dalam sistem.',
            'code.max' => 'Kode mata kuliah maksimal 50 karakter.',
            'name.required' => 'Nama mata kuliah wajib diisi.',
            'name.max' => 'Nama mata kuliah maksimal 255 karakter.',
            'credit_hours.required' => 'Bobot SKS wajib ditentukan.',
            'credit_hours.integer' => 'Bobot SKS harus berupa angka.',
            'credit_hours.min' => 'Bobot SKS minimal 1.',
            'credit_hours.max' => 'Bobot SKS maksimal 12.',
        ]);

        $course = Course::create([
            'department_id' => $departmentId,
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'credit_hours' => (int) $validated['credit_hours'],
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('kajur.courses.index')
            ->with('success', "Mata kuliah \"{$course->code} - {$course->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified course (redirect to edit).
     */
    public function show(Course $course): RedirectResponse
    {
        return redirect()->route('kajur.courses.edit', $course);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        $user = $this->authorizeKajur();

        // Enforce department boundary
        if ($user->role === 'kajur' && $course->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah mata kuliah jurusan lain.');
        }

        $department = $course->department;

        return view('kajur.courses.edit', compact('course', 'department'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $user = $this->authorizeKajur();

        // Enforce department boundary
        if ($user->role === 'kajur' && $course->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah mata kuliah jurusan lain.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($course->id)],
            'name' => ['required', 'string', 'max:255'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'code.required' => 'Kode mata kuliah wajib diisi.',
            'code.unique' => 'Kode mata kuliah sudah digunakan oleh mata kuliah lain.',
            'code.max' => 'Kode mata kuliah maksimal 50 karakter.',
            'name.required' => 'Nama mata kuliah wajib diisi.',
            'name.max' => 'Nama mata kuliah maksimal 255 karakter.',
            'credit_hours.required' => 'Bobot SKS wajib ditentukan.',
            'credit_hours.integer' => 'Bobot SKS harus berupa angka.',
            'credit_hours.min' => 'Bobot SKS minimal 1.',
            'credit_hours.max' => 'Bobot SKS maksimal 12.',
        ]);

        $course->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'credit_hours' => (int) $validated['credit_hours'],
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('kajur.courses.index')
            ->with('success', "Mata kuliah \"{$course->code} - {$course->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $user = $this->authorizeKajur();

        // Enforce department boundary
        if ($user->role === 'kajur' && $course->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus mata kuliah jurusan lain.');
        }

        $codeName = "{$course->code} - {$course->name}";
        $course->delete();

        return redirect()
            ->route('kajur.courses.index')
            ->with('success', "Mata kuliah \"{$codeName}\" berhasil dihapus.");
    }
}

