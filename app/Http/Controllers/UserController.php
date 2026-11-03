<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $query = User::with(['department', 'department.faculty']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $departments = Department::with('faculty')->orderBy('name')->get();
        $roles = [
            'super_admin' => 'Super Admin',
            'kajur' => 'Kepala Jurusan',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
        ];

        return view('superadmin.users.create', compact('departments', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:super_admin,kajur,dosen,mahasiswa'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role pengguna yang dipilih tidak valid.',
            'department_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?: null,
            'password' => 'password', // Default password
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan dengan password default "password".');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $departments = Department::with('faculty')->orderBy('name')->get();
        $roles = [
            'super_admin' => 'Super Admin',
            'kajur' => 'Kepala Jurusan',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
        ];

        return view('superadmin.users.edit', compact('user', 'departments', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', 'in:super_admin,kajur,dosen,mahasiswa'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role pengguna yang dipilih tidak valid.',
            'department_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?: null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
