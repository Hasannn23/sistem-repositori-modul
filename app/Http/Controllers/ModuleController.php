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
        $search = $request->query('search');
        $selectedClass = null;

        $user = Auth::user();

        // Jika super_admin, dapat melihat modul semua kelas
        if ($user->role === 'super_admin') {
            $myClasses = CourseClass::orderBy('name')->get();

            $query = Module::with(['courseClass', 'courseClass.dosen']);

            if ($classId) {
                $selectedClass = CourseClass::findOrFail($classId);
                $query->where('course_class_id', $classId);
            }

            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }

            $modules = $query->latest()->paginate(10)->withQueryString();

            return view('dosen.modules.index', compact('modules', 'selectedClass', 'myClasses', 'search'));
        }

        // Lecturer's classes for the filter / upload modal dropdown
        $myClasses = CourseClass::where('dosen_id', $user->id)
            ->orderBy('name')
            ->get();

        $query = Module::query();

        if ($classId) {
            $selectedClass = CourseClass::where('id', $classId)
                ->where('dosen_id', $user->id)
                ->firstOrFail();

            $query->where('course_class_id', $classId);
        } else {
            $myClassIds = $myClasses->pluck('id');
            $query->whereIn('course_class_id', $myClassIds)->with('courseClass');
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $modules = $query->latest()->paginate(10)->withQueryString();

        return view('dosen.modules.index', compact('modules', 'selectedClass', 'myClasses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): RedirectResponse
    {
        if (Auth::user()?->role === 'dosen') {
            return redirect()->route('sayembaras.index')
                ->with('info', 'Penyusunan modul baru saat ini dilakukan melalui jalur Sayembara Modul prodi.');
        }

        return redirect()->route('modules.index', ['class_id' => $request->query('class_id')]);
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $fileType = strtolower($request->input('file_type', $request->input('upload_type', 'pdf')));

        if ($fileType === 'link' || $request->filled('link_url')) {
            $request->validate([
                'course_class_id' => ['required', 'exists:course_classes,id'],
                'title' => ['required', 'string', 'max:255'],
                'link_url' => ['required', 'url', 'max:1000'],
            ], [
                'course_class_id.required' => 'Ruang kelas wajib dipilih.',
                'course_class_id.exists' => 'Ruang kelas yang dipilih tidak valid.',
                'title.required' => 'Judul modul wajib diisi.',
                'title.max' => 'Judul modul maksimal 255 karakter.',
                'link_url.required' => 'Tautan / link modul wajib diisi.',
                'link_url.url' => 'Format tautan tidak valid (harus diawali http:// atau https://).',
                'link_url.max' => 'Tautan modul maksimal 1000 karakter.',
            ]);

            $courseClass = CourseClass::where('id', $request->input('course_class_id'))
                ->when(Auth::user()->role !== 'super_admin', function ($q) {
                    $q->where('dosen_id', Auth::id());
                })
                ->firstOrFail();

            Module::create([
                'author_id' => Auth::id(),
                'course_id' => $courseClass->course_id,
                'course_class_id' => $courseClass->id,
                'title' => $request->input('title'),
                'storage_type' => 'link',
                'file_path' => $request->input('link_url'),
                'is_locked' => true,
                'status' => 'draft',
            ]);

            return redirect()
                ->route('modules.index', ['class_id' => $courseClass->id])
                ->with('success', 'Modul praktikum berhasil ditambahkan berupa tautan/link dalam status Draft (Terkunci)!');
        }

        // Default: File PDF
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

        // Ensure the course class belongs to the currently logged-in lecturer (or super_admin)
        $courseClass = CourseClass::where('id', $request->input('course_class_id'))
            ->when(Auth::user()->role !== 'super_admin', function ($q) {
                $q->where('dosen_id', Auth::id());
            })
            ->firstOrFail();

        // Upload file using Laravel Storage (disk 'public', folder 'modules')
        $uploadedFile = $request->file('file');
        $filePath = $uploadedFile->store('modules', 'public');

        // Save module with status 'draft', is_locked true, and storage_type 'pdf'
        Module::create([
            'author_id' => Auth::id(),
            'course_id' => $courseClass->course_id,
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
        // Check authorization: lecturer must own the class, staff (admin/kajur), or any authenticated user for approved modules
        $courseClass = $module->courseClass;
        $user = Auth::user();

        $isLecturer = $courseClass && $courseClass->dosen_id === $user?->id;
        $isStaff = in_array($user?->role, ['super_admin', 'kajur']);
        $isApprovedAccessible = $user && $module->status === 'approved';

        if (! $isLecturer && ! $isStaff && ! $isApprovedAccessible) {
            abort(403, 'Anda tidak memiliki hak akses ke modul ini.');
        }

        if ($module->storage_type === 'pdf' && Storage::disk('public')->exists($module->file_path)) {
            return response()->file(Storage::disk('public')->path($module->file_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.basename($module->file_path).'"',
            ]);
        }

        if (in_array($module->storage_type, ['link', 'gdrive']) || str_starts_with($module->file_path, 'http://') || str_starts_with($module->file_path, 'https://')) {
            return redirect()->away($module->file_path);
        }

        return redirect()->back()->with('error', 'File dokumen PDF tidak ditemukan di server.');
    }

    /**
     * Display the LMS multi-meeting module reader view.
     */
    public function showLms(Module $module): View
    {
        $courseClass = $module->courseClass;
        $user = Auth::user();

        $isAuthor = $module->author_id === $user?->id;
        $isLecturer = ($courseClass && $courseClass->dosen_id === $user?->id) || $isAuthor;
        $isStaff = in_array($user?->role, ['super_admin', 'kajur']);
        $isApprovedAccessible = $user && $module->status === 'approved';

        if (! $isLecturer && ! $isStaff && ! $isApprovedAccessible) {
            abort(403, 'Anda tidak memiliki hak akses ke modul ini.');
        }

        $module->load([
            'course.department',
            'courseClass.dosen.department',
            'author.department',
            'sayembara',
            'meetings.items',
        ]);

        return view('modules.show_lms', compact('module'));
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
        if ($courseClass && $courseClass->dosen_id !== Auth::id() && Auth::user()->role !== 'super_admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk merevisi modul ini.');
        }

        if ($module->status !== 'revision') {
            return redirect()->back()->with('error', 'Modul tidak dalam status revisi.');
        }

        $fileType = strtolower($request->input('file_type', $request->input('upload_type', in_array($module->storage_type, ['link', 'gdrive']) ? 'link' : 'pdf')));

        if ($fileType === 'link' || $request->filled('link_url')) {
            $request->validate([
                'link_url' => ['required', 'url', 'max:1000'],
            ], [
                'link_url.required' => 'Tautan / link modul perbaikan wajib diisi.',
                'link_url.url' => 'Format tautan tidak valid (harus diawali http:// atau https://).',
                'link_url.max' => 'Tautan modul maksimal 1000 karakter.',
            ]);

            if ($module->storage_type === 'pdf' && $module->file_path && Storage::disk('public')->exists($module->file_path)) {
                Storage::disk('public')->delete($module->file_path);
            }

            $module->storage_type = 'link';
            $module->file_path = $request->input('link_url');
            $module->status = 'waiting_review';
            $module->save();

            return redirect()->back()->with('success', 'Tautan modul perbaikan berhasil diajukan kembali ke Ketua Jurusan untuk ditinjau.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB (5120 KB)
        ], [
            'file.required' => 'File modul PDF perbaikan wajib diunggah.',
            'file.file' => 'Berkas yang diunggah harus berupa file.',
            'file.mimes' => 'Format file modul harus berupa PDF (.pdf).',
            'file.max' => 'Ukuran file modul maksimal adalah 5MB.',
        ]);

        if ($module->storage_type === 'pdf' && $module->file_path && Storage::disk('public')->exists($module->file_path)) {
            Storage::disk('public')->delete($module->file_path);
        }

        $uploadedFile = $request->file('file');
        $filePath = $uploadedFile->store('modules', 'public');

        $module->storage_type = 'pdf';
        $module->file_path = $filePath;
        $module->status = 'waiting_review';
        $module->save();

        return redirect()->back()->with('success', 'Modul revisi berhasil diunggah dan diajukan kembali ke Ketua Jurusan untuk ditinjau.');
    }
}
