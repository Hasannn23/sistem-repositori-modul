<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display the student dashboard with join form and joined classes.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Ambil daftar kelas yang sudah diikuti mahasiswa beserta relasi dosen dan jumlah modul approved
        $joinedClasses = $user->joinedClasses()
            ->with('dosen')
            ->withCount(['modules' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->latest('course_class_user.created_at')
            ->get();

        return view('mahasiswa.dashboard', compact('joinedClasses'));
    }

    /**
     * Join a course class using referral code.
     */
    public function join(Request $request): RedirectResponse
    {
        // Validasi input referral_code (wajib)
        $validated = $request->validate([
            'referral_code' => ['required', 'string'],
        ], [
            'referral_code.required' => 'Kode referral wajib diisi.',
        ]);

        $referralCode = strtoupper(trim($validated['referral_code']));

        // Cari CourseClass berdasarkan kode referral tersebut
        $courseClass = CourseClass::where('referral_code', $referralCode)->first();

        // Jika tidak ketemu, kembalikan error "Kelas tidak ditemukan"
        if (! $courseClass) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kelas tidak ditemukan.');
        }

        /** @var User $user */
        $user = Auth::user();

        // Cek apakah user sudah bergabung
        $alreadyJoined = $user->joinedClasses()
            ->where('course_classes.id', $courseClass->id)
            ->exists();

        if ($alreadyJoined) {
            return redirect()->back()
                ->with('error', 'Anda sudah bergabung di kelas '.$courseClass->name.'.');
        }

        // Jika belum, gunakan attach() untuk menyimpan ke tabel pivot
        $user->joinedClasses()->attach($courseClass->id);

        return redirect()->back()
            ->with('success', 'Berhasil bergabung ke kelas '.$courseClass->name.'!');
    }

    /**
     * Display class details and approved modules.
     */
    public function showClass(CourseClass $courseClass, Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Mahasiswa yang bisa mengakses halaman ini HANYA yang sudah terdaftar di kelas tersebut
        $isEnrolled = $user->joinedClasses()
            ->where('course_classes.id', $courseClass->id)
            ->exists();

        if (! $isEnrolled && ! in_array($user->role, ['super_admin'])) {
            abort(403, 'Anda belum terdaftar di kelas ini.');
        }

        // Load relasi dosen
        $courseClass->load('dosen');
        $search = $request->query('search');

        // Tampilkan daftar Modul yang berstatus 'approved'
        $modules = $courseClass->modules()
            ->where('status', 'approved')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.class.show', compact('courseClass', 'modules', 'search'));
    }
}
