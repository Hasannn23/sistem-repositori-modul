<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        Ruang Dosen
                    </span>
                    <span class="text-xs text-gray-500 font-medium">
                        Manajemen Kelas & Modul
                    </span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Daftar Ruang Kelas') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola ruang kelas perkuliahan, bagikan kode referral ke mahasiswa, dan unggah modul praktikum.
                </p>
            </div>

            <!-- Tombol Tambah Kelas Baru (Buka Modal Breeze) -->
            <div>
                <button
                    type="button"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'create-class')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-sm shadow-emerald-600/30 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kelas Baru</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ 
        copiedCode: null,
        copyToClipboard(code) {
            navigator.clipboard.writeText(code);
            this.copiedCode = code;
            setTimeout(() => { this.copiedCode = null; }, 2000);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notification -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm transition">
                    <div class="p-1 bg-emerald-100 text-emerald-700 rounded-lg shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex items-start gap-3 shadow-sm transition">
                    <div class="p-1 bg-red-100 text-red-700 rounded-lg shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-900">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Statistik -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kelas Saya</span>
                        <p class="text-2xl font-extrabold text-gray-900 leading-none mt-1">{{ $classes->count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Modul Terunggah</span>
                        <p class="text-2xl font-extrabold text-gray-900 leading-none mt-1">{{ $classes->sum('modules_count') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akun Dosen</span>
                        <p class="text-sm font-bold text-emerald-600 leading-none mt-1.5">Aktif & Terverifikasi</p>
                    </div>
                </div>
            </div>

            <!-- Daftar Ruang Kelas (Grid) -->
            @if ($classes->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Ruang Kelas</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                        Anda belum membuat ruang kelas praktikum. Klik tombol di bawah untuk membuat kelas baru dan mendapatkan kode referral secara otomatis.
                    </p>
                    <button
                        type="button"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-class')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-xs transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Kelas Pertama Sekarang</span>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($classes as $class)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Top Row: Icon & Module Count Badge -->
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>{{ $class->modules_count }} Modul</span>
                                    </span>
                                </div>

                                <!-- Class Name -->
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition-colors line-clamp-2">
                                    {{ $class->name }}
                                </h3>

                                <p class="text-xs text-gray-400 mt-1 mb-5">
                                    Dibuat pada {{ $class->created_at->format('d M Y, H:i') }}
                                </p>

                                <!-- Box Kode Referral (Tebal & Mudah Disalin) -->
                                <div class="bg-gradient-to-br from-gray-50 to-emerald-50/40 rounded-xl p-3.5 border border-emerald-100">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                            Kode Referral Mahasiswa
                                        </span>
                                        <span x-show="copiedCode === '{{ $class->referral_code }}'" 
                                              x-transition 
                                              class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Tersalin!
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <!-- Kode Tebal -->
                                        <span class="font-mono text-xl font-extrabold tracking-widest text-emerald-800 select-all">
                                            {{ $class->referral_code }}
                                        </span>
                                        <!-- Tombol Salin -->
                                        <button
                                            type="button"
                                            @click="copyToClipboard('{{ $class->referral_code }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 border border-gray-200 hover:border-emerald-300 rounded-lg text-xs font-semibold shadow-2xs transition active:scale-95 focus:outline-none"
                                            title="Salin Kode Referral">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Salin</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Action Button "Kelola Modul" -->
                            <div class="px-6 py-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3">
                                <a href="{{ route('modules.index', ['class_id' => $class->id]) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs shadow-emerald-600/20 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span>Kelola Modul</span>
                                </a>

                                <!-- Delete Class Action -->
                                <form method="POST" action="{{ route('classes.destroy', $class->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruang kelas ini? Seluruh modul di dalamnya juga akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors focus:outline-none"
                                        title="Hapus Ruang Kelas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Modal Tambah Kelas Baru menggunakan Breeze x-modal -->
        <x-modal name="create-class" :show="$errors->any()" focusable maxWidth="lg">
            <form method="POST" action="{{ route('classes.store') }}">
                @csrf

                <!-- Header Modal -->
                <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                Tambah Ruang Kelas Baru
                            </h3>
                            <p class="text-xs text-gray-500">
                                Buka ruang kelas baru untuk perkuliahan/praktikum Anda.
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4">
                    <!-- Input Nama Kelas -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Ruang Kelas <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            required
                            value="{{ old('name') }}"
                            placeholder="Contoh: Praktikum Pemrograman Web (Kelas A)"
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                            autofocus />
                        @error('name')
                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Info Box Referral Code Otomatis -->
                    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 flex items-start gap-3 text-blue-900">
                        <div class="p-1 bg-blue-100 text-blue-700 rounded-lg shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-xs space-y-1">
                            <p class="font-bold text-blue-900">Kode Referral Otomatis</p>
                            <p class="text-blue-700 leading-relaxed">
                                Sistem akan membuat 6 karakter huruf kapital unik (misal: <span class="font-mono font-bold">A7KZ9X</span>) yang siap dibagikan ke mahasiswa untuk masuk ke kelas ini.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-white transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold shadow-xs shadow-emerald-600/30 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Ruang Kelas</span>
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
