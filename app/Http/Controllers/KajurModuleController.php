<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KajurModuleController extends Controller
{
    /**
     * Display a listing of modules waiting for review or approved in Kajur's department.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        $departmentId = $user->department_id;
        $tab = $request->query('tab', 'waiting'); // 'waiting' or 'approved'
        $search = $request->query('search');

        // Counts for tab badges
        $waitingCountQuery = Module::where('status', 'waiting_review');
        $approvedCountQuery = Module::where('status', 'approved');

        if ($departmentId && $user->role === 'kajur') {
            $waitingCountQuery->where(function ($q) use ($departmentId) {
                $q->whereHas('courseClass.dosen', fn ($dq) => $dq->where('department_id', $departmentId))
                    ->orWhereHas('course', fn ($cq) => $cq->where('department_id', $departmentId))
                    ->orWhereHas('sayembara', fn ($sq) => $sq->where('department_id', $departmentId))
                    ->orWhereHas('author', fn ($aq) => $aq->where('department_id', $departmentId));
            });
            $approvedCountQuery->where(function ($q) use ($departmentId) {
                $q->whereHas('courseClass.dosen', fn ($dq) => $dq->where('department_id', $departmentId))
                    ->orWhereHas('course', fn ($cq) => $cq->where('department_id', $departmentId))
                    ->orWhereHas('sayembara', fn ($sq) => $sq->where('department_id', $departmentId))
                    ->orWhereHas('author', fn ($aq) => $aq->where('department_id', $departmentId));
            });
        }

        $waitingCount = $waitingCountQuery->count();
        $approvedCount = $approvedCountQuery->count();

        // Query active tab
        $status = ($tab === 'approved') ? 'approved' : 'waiting_review';
        $query = Module::where('status', $status)
            ->with(['author.department', 'course.department', 'courseClass.dosen.department', 'sayembara.course']);

        if ($departmentId && $user->role === 'kajur') {
            $query->where(function ($q) use ($departmentId) {
                $q->whereHas('courseClass.dosen', fn ($dq) => $dq->where('department_id', $departmentId))
                    ->orWhereHas('course', fn ($cq) => $cq->where('department_id', $departmentId))
                    ->orWhereHas('sayembara', fn ($sq) => $sq->where('department_id', $departmentId))
                    ->orWhereHas('author', fn ($aq) => $aq->where('department_id', $departmentId));
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('courseClass', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('courseClass.dosen', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('author', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('sayembara', function ($sq) use ($search) {
                        $sq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $modules = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('kajur.modules.index', compact('modules', 'tab', 'waitingCount', 'approvedCount', 'search'));
    }

    /**
     * Display the full LMS Reader reviewer view for Kajur.
     */
    public function review(Module $module): View
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        // Check department boundary if user has a department assigned
        $moduleDepartmentId = $module->courseClass?->dosen?->department_id
            ?? $module->course?->department_id
            ?? $module->sayembara?->department_id
            ?? $module->author?->department_id;
        if ($user->role === 'kajur' && $user->department_id && $moduleDepartmentId && $moduleDepartmentId !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mereview modul jurusan lain.');
        }

        $module->load([
            'author.department',
            'meetings.items',
            'sayembara.course',
            'course.department',
            'courseClass.dosen',
        ]);

        return view('kajur.modules.review', compact('module'));
    }

    /**
     * Approve the module, mark as master module, and close associated sayembara.
     */
    public function approve(Module $module): RedirectResponse
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        // Check department boundary if user has a department assigned
        $moduleDepartmentId = $module->courseClass?->dosen?->department_id
            ?? $module->course?->department_id
            ?? $module->sayembara?->department_id
            ?? $module->author?->department_id;
        if ($user->role === 'kajur' && $user->department_id && $moduleDepartmentId && $moduleDepartmentId !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui modul jurusan lain.');
        }

        if ($module->sayembara && $module->sayembara->status === 'closed' && ! $module->is_master) {
            return redirect()->back()->with('error', 'Sayembara untuk modul ini sudah ditutup dan pemenang Master Modul telah ditetapkan.');
        }

        // Course determination
        $courseId = $module->course_id ?? $module->sayembara?->course_id ?? $module->courseClass?->course_id;

        if ($courseId) {
            // Reset any existing master module for this course
            Module::where('course_id', $courseId)
                ->where('id', '!=', $module->id)
                ->where('is_master', true)
                ->update(['is_master' => false]);
        }

        $module->status = 'approved';
        $module->is_master = true;
        $module->is_locked = false;
        $module->kajur_notes = null;
        $module->revision_note = null;
        $module->kajur_id = $user->id;
        if ($courseId && !$module->course_id) {
            $module->course_id = $courseId;
        }
        $module->save();

        // 3. Tutup sayembara terkait jika modul terikat sayembara
        if ($module->sayembara) {
            $module->sayembara->update([
                'status' => 'closed',
            ]);
        }

        // 4. Propagasi modul master ke seluruh kelas dari mata kuliah terkait
        if ($courseId) {
            $classes = CourseClass::where('course_id', $courseId)->get();
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

                    // Duplikasi pertemuan dan materi jika ada
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

        return redirect()->route('kajur.modules.index')
            ->with('success', "Modul \"{$module->title}\" resmi disetujui (ACC) sebagai Master Modul dan sayembara telah ditutup.");
    }

    /**
     * Reject the module with revision notes.
     */
    public function reject(Request $request, Module $module): RedirectResponse
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        // Check department boundary if user has a department assigned
        $moduleDepartmentId = $module->courseClass?->dosen?->department_id
            ?? $module->course?->department_id
            ?? $module->sayembara?->department_id
            ?? $module->author?->department_id;
        if ($user->role === 'kajur' && $user->department_id && $moduleDepartmentId && $moduleDepartmentId !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk merevisi modul jurusan lain.');
        }

        $note = $request->input('revision_note') ?? $request->input('kajur_notes');

        if (empty(trim((string) $note))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'revision_note' => 'Catatan revisi wajib diisi agar dosen dapat memperbaiki modul.',
                'kajur_notes' => 'Catatan revisi wajib diisi agar dosen dapat memperbaiki modul.',
            ]);
        }

        if (mb_strlen((string) $note) > 2000) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'revision_note' => 'Catatan revisi maksimal 2000 karakter.',
                'kajur_notes' => 'Catatan revisi maksimal 2000 karakter.',
            ]);
        }

        $module->status = 'revision';
        $module->is_locked = false;
        $module->revision_note = $note;
        $module->kajur_notes = $note;
        $module->kajur_id = $user->id;
        $module->save();

        return redirect()->route('kajur.modules.index')
            ->with('success', "Modul \"{$module->title}\" berhasil dikembalikan dengan catatan revisi.");
    }
}

