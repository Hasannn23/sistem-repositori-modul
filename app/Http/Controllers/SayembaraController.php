<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Module;
use App\Models\Sayembara;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SayembaraController extends Controller
{
    /**
     * Display a listing of sayembaras.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $status = $request->query('status'); // 'open', 'closed', or null (all)
        $search = $request->query('search');

        $query = Sayembara::with(['course', 'department', 'modules.author', 'modules.courseClass.dosen'])
            ->withCount('modules');

        if ($user->role === 'kajur' && $departmentId) {
            $query->where('department_id', $departmentId);
        } elseif ($user->role === 'dosen') {
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }
            // By default for Dosen show open first unless filtered
            if (! $status) {
                $query->where('status', 'open');
            }
        }

        if ($status && in_array($status, ['open', 'closed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $sayembaras = $query->latest()->paginate(10)->withQueryString();

        return view('sayembaras.index', compact('sayembaras', 'status', 'search'));
    }

    /**
     * Show the form for creating a new sayembara (Kajur / Super Admin only).
     */
    public function create(): View
    {
        $user = Auth::user();
        if (! in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses khusus untuk Kepala Jurusan atau Super Admin.');
        }

        $coursesQuery = Course::with('department');
        if ($user->role === 'kajur' && $user->department_id) {
            $coursesQuery->where('department_id', $user->department_id);
        }

        $courses = $coursesQuery->orderBy('name')->get();

        return view('sayembaras.create', compact('courses'));
    }

    /**
     * Store a newly created sayembara in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses khusus untuk Kepala Jurusan atau Super Admin.');
        }

        $validated = $request->validate([
            'course_id'   => ['required', 'exists:courses,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'brochure'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'course_id.required'           => 'Mata kuliah wajib dipilih.',
            'course_id.exists'             => 'Mata kuliah yang dipilih tidak valid.',
            'title.required'               => 'Judul sayembara wajib diisi.',
            'description.required'         => 'Deskripsi sayembara wajib diisi.',
            'start_date.required'          => 'Tanggal mulai sayembara wajib ditentukan.',
            'end_date.required'            => 'Batas waktu sayembara wajib ditentukan.',
            'end_date.after_or_equal'      => 'Batas waktu tidak boleh lebih awal dari tanggal mulai.',
            'brochure.max'                 => 'Berkas brosur maksimal 5 MB.',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        if ($user->role === 'kajur' && $user->department_id && $course->department_id !== $user->department_id) {
            abort(403, 'Anda hanya dapat membuka sayembara untuk mata kuliah di jurusan Anda.');
        }

        $brochurePath = null;
        if ($request->hasFile('brochure')) {
            $brochurePath = $request->file('brochure')->store('sayembaras', 'public');
        }

        $sayembara = Sayembara::create([
            'department_id' => $course->department_id ?? $user->department_id,
            'course_id'     => $course->id,
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'brochure_path' => $brochurePath,
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
            'status'        => 'open',
        ]);

        return redirect()
            ->route('sayembaras.show', $sayembara)
            ->with('success', 'Sayembara pengusulan modul berhasil dibuka untuk para Dosen!');
    }

    /**
     * Display the specified sayembara.
     */
    public function show(Sayembara $sayembara): View
    {
        $sayembara->load([
            'course.department',
            'department',
            'modules.courseClass.dosen',
            'modules.meetings.items',
            'modules.kajur',
        ]);

        $user = Auth::user();
        $myClasses = collect();
        if ($user->role === 'dosen') {
            $myClasses = CourseClass::where('dosen_id', $user->id)
                ->where(function ($q) use ($sayembara) {
                    $q->where('course_id', $sayembara->course_id)
                        ->orWhereNull('course_id');
                })
                ->orderBy('name')
                ->get();
        }

        return view('sayembaras.show', compact('sayembara', 'myClasses'));
    }

    /**
     * Display the standalone form for Dosen to initiate sayembara participation.
     */
    public function showParticipateForm(Sayembara $sayembara): View|RedirectResponse
    {
        $user = Auth::user();
        if (! in_array($user->role, ['dosen', 'super_admin'])) {
            abort(403, 'Hanya Dosen yang dapat mengakses formulir inisiasi modul sayembara ini.');
        }

        if ($sayembara->status !== 'open') {
            return redirect()->route('sayembaras.show', $sayembara)->with('error', 'Sayembara ini sudah ditutup untuk pengusulan baru.');
        }

        $sayembara->load(['course.department', 'department']);

        $myClasses = collect();
        if ($user->role === 'dosen') {
            $myClasses = CourseClass::where('dosen_id', $user->id)
                ->where(function ($q) use ($sayembara) {
                    $q->where('course_id', $sayembara->course_id)
                        ->orWhereNull('course_id');
                })
                ->orderBy('name')
                ->get();
        }

        return view('dosen.sayembara.ikuti', compact('sayembara', 'myClasses'));
    }

    /**
     * Dosen initiates a module draft proposal for this sayembara.
     */
    public function participate(Request $request, Sayembara $sayembara): RedirectResponse
    {
        $user = Auth::user();
        if (! in_array($user->role, ['dosen', 'super_admin'])) {
            abort(403, 'Hanya Dosen yang dapat mengajukan modul untuk sayembara ini.');
        }

        if ($sayembara->status !== 'open') {
            return redirect()->back()->with('error', 'Sayembara ini sudah ditutup untuk pengusulan baru.');
        }

        if ($sayembara->end_date && now()->toDateString() > $sayembara->end_date->toDateString()) {
            return redirect()->back()->with('error', 'Batas waktu pengusulan modul sayembara ini telah berakhir.');
        }

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'course_class_id' => ['nullable', 'exists:course_classes,id'],
        ], [
            'title.required' => 'Judul draf modul wajib diisi.',
            'title.max'      => 'Judul draf modul maksimal 255 karakter.',
            'description.max' => 'Deskripsi singkat maksimal 2000 karakter.',
        ]);

        $module = Module::create([
            'author_id'       => $user->id,
            'course_id'       => $sayembara->course_id,
            'sayembara_id'    => $sayembara->id,
            'course_class_id' => $validated['course_class_id'] ?? null,
            'title'           => trim($validated['title']),
            'description'     => !empty($validated['description']) ? trim($validated['description']) : null,
            'storage_type'    => 'lms',
            'file_path'       => null,
            'is_locked'       => false,
            'is_master'       => false,
            'status'          => 'draft',
        ]);

        return redirect()
            ->route('dosen.modules.builder', $module->id)
            ->with('success', 'Draf modul berhasil diinisiasi! Silakan mulai menyusun kurikulum pertemuan dan materi LMS.');
    }

    /**
     * Kajur approves a proposed module as the MASTER MODULE.
     */
    public function approveMaster(Request $request, Sayembara $sayembara, Module $module): RedirectResponse
    {
        $user = Auth::user();
        if (! in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Hanya Kepala Jurusan atau Super Admin yang dapat menetapkan Master Modul.');
        }

        if ($module->sayembara_id !== $sayembara->id) {
            abort(400, 'Modul tidak terkait dengan sayembara ini.');
        }

        if ($user->role === 'kajur' && $user->department_id && $sayembara->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui modul jurusan lain.');
        }

        if ($sayembara->status === 'closed') {
            return redirect()->back()->with('error', 'Sayembara ini sudah ditutup dan pemenang Master Modul telah ditetapkan.');
        }

        // 1. Reset master status for any previous master module in the same course
        Module::where('course_id', $sayembara->course_id)
            ->where('is_master', true)
            ->update(['is_master' => false]);

        // 2. Set this module as the approved Master Module
        $module->update([
            'is_master'   => true,
            'status'      => 'approved',
            'is_locked'   => false,
            'kajur_notes' => null,
            'kajur_id'    => $user->id,
        ]);

        // 3. Mark the sayembara as closed
        $sayembara->update([
            'status' => 'closed',
        ]);

        // 4. Automatically propagate this master module to all course_classes of the same course
        if ($sayembara->course_id) {
            $classes = CourseClass::where('course_id', $sayembara->course_id)->get();
            foreach ($classes as $class) {
                $alreadyHas = Module::where('course_class_id', $class->id)
                    ->where(function ($q) use ($module) {
                        $q->where('id', $module->id)
                          ->orWhere('title', $module->title);
                    })
                    ->exists();

                if (! $alreadyHas) {
                    $clonedModule = $module->replicate(['course_class_id']);
                    $clonedModule->course_class_id = $class->id;
                    $clonedModule->is_master = true;
                    $clonedModule->status = 'approved';
                    $clonedModule->is_locked = false;
                    $clonedModule->save();

                    // Copy module meetings & items if any
                    foreach ($module->meetings as $meeting) {
                        $clonedMeeting = $meeting->replicate(['module_id']);
                        $clonedMeeting->module_id = $clonedModule->id;
                        $clonedMeeting->save();

                        foreach ($meeting->items as $item) {
                            $clonedItem = $item->replicate(['module_meeting_id']);
                            $clonedItem->module_meeting_id = $clonedMeeting->id;
                            $clonedItem->save();
                        }
                    }
                }
            }
        }

        return redirect()
            ->route('sayembaras.show', $sayembara)
            ->with('success', "Modul \"{$module->title}\" resmi ditetapkan sebagai MASTER MODUL! Seluruh kelas mata kuliah terkait otomatis memakai modul ini.");
    }

    /**
     * Close a sayembara manually.
     */
    public function close(Sayembara $sayembara): RedirectResponse
    {
        $user = Auth::user();
        if (! in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses khusus untuk Kepala Jurusan atau Super Admin.');
        }

        $sayembara->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Sayembara berhasil ditutup.');
    }
}

