<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\ModuleMeeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LmsBuilderController extends Controller
{
    /**
     * Authorize that the current user can edit this module in the LMS builder.
     */
    protected function authorizeModuleAccess(Module $module): void
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return;
        }

        $isAuthor = $module->author_id === $user->id;
        $isClassDosen = $module->courseClass && $module->courseClass->dosen_id === $user->id;

        if (! $isAuthor && ! $isClassDosen) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyunting modul ini.');
        }

        // Check if module is already approved or master (permanently locked)
        if ($module->status === 'approved' || $module->is_master) {
            abort(403, 'Modul resmi yang telah disahkan oleh Program Studi bersifat permanen dan terkunci dari perubahan.');
        }

        // Check if associated sayembara is already closed
        if ($module->sayembara && $module->sayembara->status === 'closed') {
            abort(403, 'Sayembara untuk modul ini telah ditutup. Penyuntingan tidak lagi diizinkan.');
        }

        // Check if module is locked under review
        if ($module->status === 'waiting_review' || ($module->is_locked && ! in_array($module->status, ['draft', 'revision']))) {
            abort(403, 'Modul ini sedang dalam proses peninjauan oleh Kajur dan berstatus terkunci.');
        }
    }

    /**
     * Show the full-screen LMS builder workspace.
     */
    public function edit(Module $module): View
    {
        $this->authorizeModuleAccess($module);

        $module->load([
            'course.department',
            'courseClass.dosen',
            'sayembara',
            'meetings.items' => function ($q) {
                $q->orderBy('order_index')->orderBy('id');
            },
        ]);

        if (view()->exists('dosen.modules.builder')) {
            return view('dosen.modules.builder', compact('module'));
        }

        return view('modules.builder', compact('module'));
    }

    /**
     * Silent autosave endpoint for module basic info.
     */
    public function autosave(Request $request, Module $module): JsonResponse
    {
        $this->authorizeModuleAccess($module);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $module->update([
            'title' => $validated['title'],
        ]);

        return response()->json([
            'status'     => 'success',
            'message'    => 'Draft tersimpan otomatis',
            'saved_at'   => now()->format('H:i:s'),
            'updated_at' => $module->updated_at->diffForHumans(),
        ]);
    }

    /**
     * Store a new meeting block (Pertemuan) for this module.
     */
    public function storeMeeting(Request $request, Module $module): RedirectResponse
    {
        $this->authorizeModuleAccess($module);

        $nextMeetingNumber = ($module->meetings()->max('meeting_number') ?? 0) + 1;
        if ($nextMeetingNumber > 16) {
            return redirect()->back()->with('error', 'Jumlah pertemuan maksimal adalah 16.');
        }

        $validated = $request->validate([
            'title'          => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'meeting_number' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $meetingNumber = $validated['meeting_number'] ?? $nextMeetingNumber;
        $title = ($validated['title'] ?? null) ?: "Pertemuan {$meetingNumber}";

        ModuleMeeting::create([
            'module_id'      => $module->id,
            'meeting_number' => $meetingNumber,
            'title'          => $title,
            'description'    => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('modules.builder', $module)
            ->with('success', "Pertemuan {$meetingNumber} berhasil ditambahkan!");
    }

    /**
     * Update a meeting block title or description.
     */
    public function updateMeeting(Request $request, ModuleMeeting $meeting): RedirectResponse
    {
        $this->authorizeModuleAccess($meeting->module);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $meeting->update($validated);

        return redirect()
            ->route('modules.builder', $meeting->module_id)
            ->with('success', "Informasi {$meeting->title} berhasil diperbarui!");
    }

    /**
     * Delete a meeting block.
     */
    public function destroyMeeting(ModuleMeeting $meeting): RedirectResponse
    {
        $module = $meeting->module;
        $this->authorizeModuleAccess($module);

        $meetingTitle = $meeting->title;
        $meeting->items()->delete();
        $meeting->delete();

        return redirect()
            ->route('modules.builder', $module)
            ->with('success', "{$meetingTitle} beserta materi di dalamnya berhasil dihapus.");
    }

    /**
     * Add a new learning item (Text, Video Link, File) into a meeting.
     */
    public function storeItem(Request $request, ModuleMeeting $meeting): RedirectResponse
    {
        $module = $meeting->module;
        $this->authorizeModuleAccess($module);

        $type = $request->input('type');
        $orderIndex = ($meeting->items()->max('order_index') ?? 0) + 1;

        if ($type === 'text') {
            $validated = $request->validate([
                'title'        => ['required', 'string', 'max:255'],
                'content_body' => ['required', 'string'],
            ], [
                'title.required'        => 'Judul materi wajib diisi.',
                'content_body.required' => 'Isi teks instruksi / materi wajib diisi.',
            ]);

            ModuleItem::create([
                'module_meeting_id' => $meeting->id,
                'title'             => $validated['title'],
                'type'              => 'text',
                'content_body'      => $validated['content_body'],
                'order_index'       => $orderIndex,
            ]);
        } elseif ($type === 'video_link') {
            $validated = $request->validate([
                'title'        => ['required', 'string', 'max:255'],
                'video_url'    => ['required', 'url', 'max:1000'],
                'content_body' => ['nullable', 'string', 'max:1000'],
            ], [
                'title.required'     => 'Judul video wajib diisi.',
                'video_url.required' => 'URL video YouTube wajib diisi.',
                'video_url.url'      => 'Format link URL tidak valid.',
            ]);

            ModuleItem::create([
                'module_meeting_id' => $meeting->id,
                'title'             => $validated['title'],
                'type'              => 'video_link',
                'content_body'      => $validated['content_body'] ?? null,
                'file_path'         => $validated['video_url'],
                'order_index'       => $orderIndex,
            ]);
        } elseif ($type === 'file') {
            $validated = $request->validate([
                'title'        => ['required', 'string', 'max:255'],
                'file'         => ['required', 'file', 'mimes:pdf,ppt,pptx,zip,docx', 'max:10240'], // 10MB
                'content_body' => ['nullable', 'string', 'max:1000'],
            ], [
                'title.required' => 'Judul berkas materi wajib diisi.',
                'file.required'  => 'Berkas file materi wajib diunggah.',
                'file.mimes'     => 'Format berkas harus berupa PDF, PPT, PPTX, DOCX, atau ZIP.',
                'file.max'       => 'Ukuran berkas maksimal 10 MB.',
            ]);

            $path = $request->file('file')->store('module_items', 'public');

            ModuleItem::create([
                'module_meeting_id' => $meeting->id,
                'title'             => $validated['title'],
                'type'              => 'file',
                'content_body'      => $validated['content_body'] ?? null,
                'file_path'         => $path,
                'order_index'       => $orderIndex,
            ]);
        } else {
            return redirect()->back()->with('error', 'Tipe materi tidak dikenali.');
        }

        return redirect()
            ->route('modules.builder', $module)
            ->with('success', 'Materi pembelajaran berhasil ditambahkan ke ' . $meeting->title);
    }

    /**
     * Delete an individual learning item.
     */
    public function destroyItem(ModuleItem $item): RedirectResponse
    {
        $module = $item->meeting->module;
        $this->authorizeModuleAccess($module);

        $title = $item->title;
        if ($item->type === 'file' && $item->file_path && Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()
            ->route('modules.builder', $module)
            ->with('success', "Materi '{$title}' berhasil dihapus.");
    }

    /**
     * Lecturer finalizes and submits module to Kajur (locks module).
     */
    public function submit(Request $request, Module $module): RedirectResponse
    {
        if ($module->sayembara) {
            if ($module->sayembara->status !== 'open') {
                return redirect()->back()->with('error', 'Sayembara ini telah ditutup. Pengajuan modul tidak lagi diizinkan.');
            }
            if ($module->sayembara->end_date && now()->toDateString() > $module->sayembara->end_date->toDateString()) {
                return redirect()->back()->with('error', 'Batas waktu pengajuan modul sayembara ini telah berakhir.');
            }
        }

        $this->authorizeModuleAccess($module);

        if ($module->meetings()->count() === 0) {
            return redirect()->back()->with('error', 'Modul minimal harus memiliki 1 pertemuan sebelum diajukan ke Kajur.');
        }

        $module->update([
            'is_locked' => true,
            'status'    => 'waiting_review',
        ]);

        if ($module->sayembara_id) {
            return redirect()
                ->route('sayembaras.show', $module->sayembara_id)
                ->with('success', 'Modul berhasil diajukan ke Kepala Jurusan untuk dievaluasi!');
        }

        return redirect()
            ->route('modules.lms', $module)
            ->with('success', 'Modul berhasil diajukan ke Kepala Jurusan untuk dievaluasi!');
    }
}

