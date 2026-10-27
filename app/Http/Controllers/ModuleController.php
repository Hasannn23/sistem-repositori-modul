<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules for a course class or all modules for the lecturer.
     */
    public function index(Request $request): View
    {
        $classId = $request->query('class_id');
        $selectedClass = null;

        // Lecturer's classes for the filter / upload modal dropdown
        $myClasses = CourseClass::where('dosen_id', Auth::id())
            ->orderBy('name')
            ->get();

        if ($classId) {
            $selectedClass = CourseClass::where('id', $classId)
                ->where('dosen_id', Auth::id())
                ->firstOrFail();

            $modules = Module::where('course_class_id', $classId)
                ->latest()
                ->get();
        } else {
            $myClassIds = $myClasses->pluck('id');
            $modules = Module::whereIn('course_class_id', $myClassIds)
                ->with('courseClass')
                ->latest()
                ->get();
        }

        return view('dosen.modules.index', compact('modules', 'selectedClass', 'myClasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('modules.index', ['class_id' => $request->query('class_id')]);
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'course_class_id' => ['required', 'exists:course_classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB (5120 KB)
        ], [
            'course_class_id.required' => 'Ruang kelas wajib dipilih.',
            'course_class_id.exists' => 'Ruang kelas yang dipilih tidak valid.',
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
            'file.required' => 'File modul PDF wajib diunggah.',
            'file.file' => 'Berkas yang diunggah harus berupa file.',
            'file.mimes' => 'Format file modul harus berupa PDF (.pdf).',
            'file.max' => 'Ukuran file modul maksimal adalah 5MB.',
        ]);

        // Ensure the course class belongs to the currently logged-in lecturer
        $courseClass = CourseClass::where('id', $request->input('course_class_id'))
            ->where('dosen_id', Auth::id())
            ->firstOrFail();

        // Upload file using Laravel Storage (disk 'public', folder 'modules')
        $uploadedFile = $request->file('file');
        $filePath = $uploadedFile->store('modules', 'public');

        // Save module with status 'draft', is_locked true, and storage_type 'pdf'
        Module::create([
            'course_class_id' => $courseClass->id,
            'title' => $request->input('title'),
            'storage_type' => 'pdf',
            'file_path' => $filePath,
            'is_locked' => true,
            'status' => 'draft',
        ]);

        return redirect()
            ->route('modules.index', ['class_id' => $courseClass->id])
            ->with('success', 'Modul praktikum berhasil diunggah dalam status Draft (Terkunci)!');
    }

    /**
     * Display or stream the specified module file.
     */
    public function show(Module $module): BinaryFileResponse|RedirectResponse
    {
        // Check authorization: lecturer must own the class or be admin/kajur
        $courseClass = $module->courseClass;
        if ($courseClass && $courseClass->dosen_id !== Auth::id() && !in_array(Auth::user()->role, ['super_admin', 'kajur'])) {
            abort(403, 'Anda tidak memiliki hak akses ke modul ini.');
        }

        if ($module->storage_type === 'pdf' && Storage::disk('public')->exists($module->file_path)) {
            return response()->file(Storage::disk('public')->path($module->file_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.basename($module->file_path).'"',
            ]);
        }

        return redirect()->back()->with('error', 'File dokumen PDF tidak ditemukan di server.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module): RedirectResponse
    {
        return redirect()->route('modules.index', ['class_id' => $module->course_class_id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module): RedirectResponse
    {
        $courseClass = $module->courseClass;
        if ($courseClass->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah modul ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
        ]);

        $module->update(['title' => $validated['title']]);

        return redirect()->back()->with('success', 'Judul modul berhasil diperbarui.');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module): RedirectResponse
    {
        $courseClass = $module->courseClass;
        if ($courseClass->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus modul ini.');
        }

        // Delete the physical file from public disk if exists
        if ($module->storage_type === 'pdf' && $module->file_path && Storage::disk('public')->exists($module->file_path)) {
            Storage::disk('public')->delete($module->file_path);
        }

        $classId = $module->course_class_id;
        $module->delete();

        return redirect()
            ->route('modules.index', ['class_id' => $classId])
            ->with('success', 'Modul praktikum berhasil dihapus.');
    }

    /**
     * Submit the specified module to Kajur for review.
     */
    public function submit(Module $module): RedirectResponse
    {
        $courseClass = $module->courseClass;
        if ($courseClass && $courseClass->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan modul ini.');
        }

        if (in_array($module->status, ['draft', 'revision'])) {
            $module->status = 'waiting_review';
            $module->save();

            return redirect()->back()->with('success', 'Modul praktikum berhasil diajukan ke Ketua Jurusan untuk ditinjau.');
        }

        return redirect()->back()->with('error', 'Modul tidak dapat diajukan pada status saat ini.');
    }

    /**
     * Update module with a revised PDF file and resubmit to Kajur for review.
     */
    public function updateRevision(Request $request, Module $module): RedirectResponse
    {
        $courseClass = $module->courseClass;
        if ($courseClass && $courseClass->dosen_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk merevisi modul ini.');
        }

        if ($module->status !== 'revision') {
            return redirect()->back()->with('error', 'Modul tidak dalam status revisi.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB (5120 KB)
        ], [
            'file.required' => 'File modul PDF perbaikan wajib diunggah.',
            'file.file' => 'Berkas yang diunggah harus berupa file.',
            'file.mimes' => 'Format file modul harus berupa PDF (.pdf).',
            'file.max' => 'Ukuran file modul maksimal adalah 5MB.',
        ]);

        if ($module->file_path) {
            Storage::disk('public')->delete($module->file_path);
        }

        $uploadedFile = $request->file('file');
        $filePath = $uploadedFile->store('modules', 'public');

        $module->file_path = $filePath;
        $module->status = 'waiting_review';
        $module->save();

        return redirect()->back()->with('success', 'Modul revisi berhasil diunggah dan diajukan kembali ke Ketua Jurusan untuk ditinjau.');
    }
}
