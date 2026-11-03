<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyController extends Controller
{
    /**
     * Display a listing of faculties.
     */
    public function index(): View
    {
        $faculties = Faculty::withCount('departments')
            ->latest()
            ->paginate(10);

        return view('superadmin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create(): View
    {
        return view('superadmin.faculties.create');
    }

    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:faculties,name'],
        ], [
            'name.required' => 'Nama fakultas wajib diisi.',
            'name.unique' => 'Nama fakultas sudah terdaftar.',
            'name.max' => 'Nama fakultas maksimal 255 karakter.',
        ]);

        Faculty::create($validated);

        return redirect()->route('faculties.index')
            ->with('success', 'Fakultas berhasil ditambahkan.');
    }

    /**
     * Display the specified faculty.
     */
    public function show(Faculty $faculty): RedirectResponse
    {
        return redirect()->route('faculties.edit', $faculty);
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty): View
    {
        return view('superadmin.faculties.edit', compact('faculty'));
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faculties', 'name')->ignore($faculty->id),
            ],
        ], [
            'name.required' => 'Nama fakultas wajib diisi.',
            'name.unique' => 'Nama fakultas sudah terdaftar.',
            'name.max' => 'Nama fakultas maksimal 255 karakter.',
        ]);

        $faculty->update($validated);

        return redirect()->route('faculties.index')
            ->with('success', 'Fakultas berhasil diperbarui.');
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();

        return redirect()->route('faculties.index')
            ->with('success', 'Fakultas berhasil dihapus.');
    }
}
