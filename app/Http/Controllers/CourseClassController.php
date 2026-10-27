<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
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
        $classes = CourseClass::where('dosen_id', Auth::id())
            ->withCount('modules')
            ->latest()
            ->get();

        return view('dosen.classes.index', compact('classes'));
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama ruang kelas wajib diisi.',
            'name.max' => 'Nama ruang kelas maksimal 255 karakter.',
        ]);

        // Generate unique referral code (uppercase 6-character random string)
        do {
            $referralCode = Str::upper(Str::random(6));
        } while (CourseClass::where('referral_code', $referralCode)->exists());

        CourseClass::create([
            'dosen_id' => Auth::id(),
            'name' => $validated['name'],
            'referral_code' => $referralCode,
        ]);

        return redirect()->route('classes.index')->with('success', 'Ruang kelas baru berhasil dibuat!');
    }

    /**
     * Display the specified course class and its modules.
     */
    public function show(CourseClass $class): RedirectResponse|View
    {
        if ($class->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke ruang kelas ini.');
        }

        return redirect()->route('modules.index', ['class_id' => $class->id]);
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
