<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KajurModuleController extends Controller
{
    /**
     * Display a listing of modules waiting for review in Kajur's department.
     */
    public function index(): View
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        $departmentId = $user->department_id;

        $query = Module::where('status', 'waiting_review')
            ->with(['courseClass.dosen.department']);

        if ($departmentId) {
            $query->whereHas('courseClass.dosen', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $modules = $query->latest('updated_at')->get();

        return view('kajur.modules.index', compact('modules'));
    }

    /**
     * Approve the module and unlock it for public access.
     */
    public function approve(Module $module): RedirectResponse
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kajur', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Kepala Jurusan.');
        }

        // Check department boundary if user has a department assigned
        $moduleDepartmentId = $module->courseClass?->dosen?->department_id;
        if ($user->role === 'kajur' && $user->department_id && $moduleDepartmentId !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui modul jurusan lain.');
        }

        $module->status = 'approved';
        $module->is_locked = false;
        $module->kajur_notes = null;
        $module->kajur_id = $user->id;
        $module->save();

        return redirect()->back()->with('success', "Modul \"{$module->title}\" berhasil disetujui (ACC) dan dapat diakses publik.");
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
        $moduleDepartmentId = $module->courseClass?->dosen?->department_id;
        if ($user->role === 'kajur' && $user->department_id && $moduleDepartmentId !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk merevisi modul jurusan lain.');
        }

        $validated = $request->validate([
            'kajur_notes' => ['required', 'string', 'max:2000'],
        ], [
            'kajur_notes.required' => 'Catatan revisi wajib diisi agar dosen dapat memperbaiki modul.',
            'kajur_notes.max' => 'Catatan revisi maksimal 2000 karakter.',
        ]);

        $module->status = 'revision';
        $module->kajur_notes = $validated['kajur_notes'];
        $module->kajur_id = $user->id;
        $module->save();

        return redirect()->back()->with('success', "Modul \"{$module->title}\" berhasil dikembalikan dengan catatan revisi.");
    }
}
