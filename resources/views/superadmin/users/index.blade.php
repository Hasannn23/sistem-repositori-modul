<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Pengguna') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manajemen akun pengguna, hak akses, dan penetapan role dalam sistem repositori modul.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('users.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Pengguna</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Alerts --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Sukses</h4>
                        <p class="text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Peringatan</h4>
                        <p class="text-sm mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Pengguna</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Total {{ $users->total() }} akun terdaftar</p>
                    </div>

                    <!-- Form Pencarian & Filter Pengguna -->
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <select 
                            name="role" 
                            onchange="this.form.submit()" 
                            class="text-xs sm:text-sm rounded-xl border-gray-300 py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500 text-gray-700 bg-white"
                        >
                            <option value="">Semua Role</option>
                            <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="kajur" {{ request('role') === 'kajur' ? 'selected' : '' }}>Kepala Jurusan</option>
                            <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        </select>

                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari..." 
                                class="text-xs sm:text-sm rounded-xl border-gray-300 py-2 pl-9 pr-3 focus:border-emerald-500 focus:ring-emerald-500 placeholder-gray-400 w-full sm:w-64"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition">
                            Cari
                        </button>

                        @if(request('search') || request('role'))
                            <a href="{{ route('users.index') }}" class="p-2 text-xs text-gray-400 hover:text-gray-600 rounded-lg text-center" title="Reset">
                                ✕
                            </a>
                        @endif
                    </form>
                </div>

                @if ($users->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-4 border border-gray-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">
                            {{ request('search') || request('role') ? 'Pengguna Tidak Ditemukan' : 'Belum Ada Pengguna' }}
                        </h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            {{ request('search') || request('role') ? 'Tidak ada akun pengguna yang sesuai dengan kriteria pencarian Anda.' : 'Klik tombol "Tambah Pengguna" di atas untuk mendaftarkan akun pertama.' }}
                        </p>
                    </div>
                @else
                    {{-- Mobile Card View (shown on small screens) --}}
                    <div class="block md:hidden divide-y divide-gray-100">
                        @foreach ($users as $index => $user)
                            <div class="p-4 hover:bg-gray-50/60 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-emerald-200/50">
                                            {{ $user->initials() ?: 'US' }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-gray-900 block leading-tight truncate">
                                                {{ $user->name }}
                                                @if (Auth::id() === $user->id)
                                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 ml-1">Anda</span>
                                                @endif
                                            </span>
                                            <span class="text-xs text-gray-500 font-mono truncate block">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <a href="{{ route('users.edit', $user) }}" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        @if (Auth::id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                    @if ($user->role === 'super_admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Super Admin</span>
                                    @elseif ($user->role === 'kajur')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Kepala Jurusan</span>
                                    @elseif ($user->role === 'dosen')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Dosen</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Mahasiswa</span>
                                    @endif
                                    @if ($user->department)
                                        <span class="text-xs text-gray-500">{{ $user->department->name }}</span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Jurusan belum ditentukan</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $user->created_at?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View (hidden on small screens) --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    <th class="py-3.5 px-6 w-16">No</th>
                                    <th class="py-3.5 px-6">Pengguna</th>
                                    <th class="py-3.5 px-6">Role</th>
                                    <th class="py-3.5 px-6">Jurusan & Fakultas</th>
                                    <th class="py-3.5 px-6">Terdaftar</th>
                                    <th class="py-3.5 px-6 text-right w-44">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($users as $index => $user)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-4 px-6 text-gray-500 font-mono text-xs">
                                            {{ $users->firstItem() + $index }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-emerald-200/50">
                                                    {{ $user->initials() ?: 'US' }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-gray-900 block leading-tight">
                                                        {{ $user->name }}
                                                        @if (Auth::id() === $user->id)
                                                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 ml-1">Anda</span>
                                                        @endif
                                                    </span>
                                                    <span class="text-xs text-gray-500 font-mono">
                                                        {{ $user->email }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            @if ($user->role === 'super_admin')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                                    Super Admin
                                                </span>
                                            @elseif ($user->role === 'kajur')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                    Kepala Jurusan
                                                </span>
                                            @elseif ($user->role === 'dosen')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Dosen
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                    Mahasiswa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">
                                            @if ($user->department)
                                                <div>
                                                    <span class="font-medium text-gray-900 block">
                                                        {{ $user->department->name }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $user->department->faculty?->name ?? 'Fakultas Belum Terhubung' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">
                                                    Belum ditentukan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 text-xs">
                                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('users.edit', $user) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 hover:border-gray-400 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                                                    title="Edit Pengguna"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                @if (Auth::id() !== $user->id)
                                                    <form
                                                        action="{{ route('users.destroy', $user) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')"
                                                        class="inline"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 text-red-700 text-xs font-semibold transition"
                                                            title="Hapus Pengguna"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
