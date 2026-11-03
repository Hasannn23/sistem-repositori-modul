<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('users.index') }}"
                class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                title="Kembali ke Daftar Pengguna"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Pengguna Baru') }}
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Daftarkan akun pengguna baru beserta penetapan role dan jurusan.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-xs">
                
                {{-- Notice Password Default --}}
                <div class="mb-6 p-4 rounded-xl bg-blue-50/80 border border-blue-200/70 flex items-start gap-3 text-blue-900">
                    <div class="p-1.5 bg-blue-100 text-blue-700 rounded-lg shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-blue-900">Informasi Password Default</p>
                        <p class="text-blue-700 leading-relaxed">
                            Pengguna baru yang dibuat dari halaman ini otomatis memiliki password default: <span class="font-mono font-bold bg-blue-100/70 px-1.5 py-0.5 rounded text-blue-900">password</span>. Password autentikasi sinkronisasi nanti dapat dikelola melalui SIAKAD.
                        </p>
                    </div>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Nama Pengguna --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Dr. Budi Santoso, M.Kom."
                            required
                            autofocus
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="Contoh: budi.santoso@kampus.ac.id"
                            required
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dropdown Role --}}
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Role / Hak Akses <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="role"
                            id="role"
                            required
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('role') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                            <option value="">-- Pilih Role Pengguna --</option>
                            @foreach ($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                    {{ $label }} ({{ $key }})
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dropdown Jurusan --}}
                    <div>
                        <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Jurusan / Program Studi <span class="text-gray-400 text-xs font-normal">(Opsional untuk Super Admin)</span>
                        </label>
                        <select
                            name="department_id"
                            id="department_id"
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('department_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                            <option value="">-- Tidak Terhubung ke Jurusan Manapun --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }} ({{ $department->faculty?->name ?? 'Fakultas -' }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('users.index') }}"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Pengguna</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
