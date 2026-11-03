<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Department::with('faculty')->withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        $departments = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.departments.index', compact('departments', 'search'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('superadmin.departments.create', compact('faculties'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
        ], [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists' => 'Fakultas yang dipilih tidak valid.',
            'name.required' => 'Nama jurusan wajib diisi.',
            'name.max' => 'Nama jurusan maksimal 255 karakter.',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): RedirectResponse
    {
        return redirect()->route('departments.edit', $department);
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('superadmin.departments.edit', compact('department', 'faculties'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
        ], [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists' => 'Fakultas yang dipilih tidak valid.',
            'name.required' => 'Nama jurusan wajib diisi.',
            'name.max' => 'Nama jurusan maksimal 255 karakter.',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
