<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Sistem Repositori & Penyusunan Modul Praktikum Perkuliahan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if (auth()->user()->role == 'kajur')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        <span>Kepala Jurusan</span>
                    </span>
                @elseif (auth()->user()->role == 'dosen')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span>Dosen Pengampu</span>
                    </span>
                @elseif (auth()->user()->role == 'super_admin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <span>Super Admin</span>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if (auth()->user()->role == 'super_admin')
                {{-- 1. Super Admin View --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 p-7 sm:p-9 text-white shadow-md">
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white font-black text-2xl shadow-inner shrink-0 ring-4 ring-rose-500/20">
                                SA
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 text-xs font-bold tracking-wider uppercase bg-rose-500/30 border border-rose-400/40 rounded-full text-rose-200">
                                        Super Administrator
                                    </span>
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                    Selamat Datang, {{ auth()->user()->name }}! 🛡️
                                </h3>
                                <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
                                    Kelola seluruh master data akademik: fakultas, jurusan, serta otorisasi hak akses pengguna sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Menu Cards Super Admin --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Card 1: Kelola Fakultas & Jurusan (Indigo) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-indigo-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-4">
                                <div class="p-3 bg-indigo-50 text-indigo-700 rounded-2xl border border-indigo-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Fakultas & Jurusan</h4>
                                    <p class="text-xs text-slate-400">Master unit organisasi</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Kelola struktur fakultas, departemen, dan program studi yang aktif di lingkungan perguruan tinggi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('faculties.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Kelola Unit</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Kelola Pengguna (Sky/Blue) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-sky-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-4">
                                <div class="p-3 bg-sky-50 text-sky-700 rounded-2xl border border-sky-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Pengguna & Akses</h4>
                                    <p class="text-xs text-slate-400">Manajemen akun & role</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Kontrol autentikasi, penetapan role (Dosen, Kajur, Mahasiswa), serta penempatan program studi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Kelola Pengguna</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 3: Daftar Modul Praktikum (Emerald) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-emerald-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-4">
                                <div class="p-3 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Repositori Modul</h4>
                                    <p class="text-xs text-slate-400">Katalog berkas & LMS</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Pantau repositori seluruh modul perkuliahan dan pastikan materi praktikum terdistribusi dengan baik.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('modules.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Katalog Modul</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role == 'kajur')
                {{-- 2. Kajur View --}}

                {{-- Hero / Welcome Banner Kajur --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-7 sm:p-9 text-white shadow-md">
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white font-black text-2xl shadow-inner shrink-0 ring-4 ring-indigo-500/20">
                                @php
                                    $kajurParts = explode(' ', trim(auth()->user()->name));
                                    $kajurInitials = '';
                                    foreach (array_slice($kajurParts, 0, 2) as $p) {
                                        $kajurInitials .= strtoupper(substr($p, 0, 1));
                                    }
                                @endphp
                                {{ $kajurInitials ?: 'KJ' }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 text-xs font-bold tracking-wider uppercase bg-indigo-500/30 border border-indigo-400/40 rounded-full text-indigo-200">
                                        Kepala Jurusan
                                    </span>
                                    <span class="px-2.5 py-0.5 text-xs font-semibold bg-white/10 border border-white/20 rounded-full text-slate-200">
                                        {{ auth()->user()->department->name ?? 'Program Studi' }}
                                    </span>
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                    Selamat Datang, {{ auth()->user()->name }}! 🏛️
                                </h3>
                                <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
                                    Kelola kurikulum prodi, verifikasi pengajuan modul praktikum melalui LMS Reader terpadu, dan tetapkan Master Modul resmi.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-wrap items-center gap-3">
                            <a href="{{ route('kajur.modules.index', ['tab' => 'waiting']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-300">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>Review Pengajuan</span>
                            </a>
                            <a href="{{ route('kajur.courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white">
                                <svg class="w-4 h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Mata Kuliah</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Grid 4 Metrik Statistik Kajur --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    {{-- Card 1: Menunggu Review (Amber Alert) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 ring-4 ring-amber-50/50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Menunggu Review</p>
                                <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">
                                    {{ $stats['waiting_reviews_count'] ?? 0 }}
                                </h4>
                                <p class="text-xs text-amber-700 font-medium mt-1">Butuh telaah & ACC Kajur</p>
                            </div>
                        </div>
                        @if (($stats['waiting_reviews_count'] ?? 0) > 0)
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                        @endif
                    </div>

                    {{-- Card 2: Master Modul Resmi (Emerald) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 ring-4 ring-emerald-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Master Modul Resmi</p>
                            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">
                                {{ $stats['approved_modules_count'] ?? 0 }}
                            </h4>
                            <p class="text-xs text-emerald-700 font-medium mt-1">Disetujui untuk prodi</p>
                        </div>
                    </div>

                    {{-- Card 3: Mata Kuliah Prodi (Indigo) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-13 h-13 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0 border border-indigo-200 ring-4 ring-indigo-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Mata Kuliah</p>
                            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">
                                {{ $stats['total_courses'] ?? 0 }}
                            </h4>
                            <p class="text-xs text-indigo-700 font-medium mt-1">Kurikulum aktif jurusan</p>
                        </div>
                    </div>

                    {{-- Card 4: Sayembara Aktif (Purple/Violet) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-13 h-13 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-200 ring-4 ring-purple-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sayembara Terbuka</p>
                            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">
                                {{ $stats['active_sayembaras_count'] ?? 0 }}
                            </h4>
                            <p class="text-xs text-purple-700 font-medium mt-1">Penyusunan modul berjalan</p>
                        </div>
                    </div>
                </div>

                {{-- Antrian Pengajuan Modul Perlu Ditinjau --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-6 bg-amber-500 rounded-full"></div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Antrian Pengajuan Modul Perlu Ditinjau</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $stats['waiting_reviews_count'] ?? 0 }} Menunggu
                            </span>
                        </div>
                        <a href="{{ route('kajur.modules.index', ['tab' => 'waiting']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 uppercase tracking-wider transition flex items-center gap-1">
                            <span>Buka Ruang Review</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    @if (isset($stats['recent_waiting_reviews']) && $stats['recent_waiting_reviews']->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                            <div class="divide-y divide-slate-100">
                                @foreach ($stats['recent_waiting_reviews'] as $module)
                                    @php
                                        $authorName = $module->author?->name ?? 'Dosen Belum Ditentukan';
                                        $courseName = $module->course->name ?? $module->sayembara->course->name ?? 'Mata Kuliah';
                                        $courseCode = $module->course->code ?? $module->sayembara->course->code ?? '-';
                                    @endphp
                                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition">
                                        <div class="flex items-start gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                @php
                                                    $parts = explode(' ', trim($authorName));
                                                    $initials = '';
                                                    foreach (array_slice($parts, 0, 2) as $p) {
                                                        $initials .= strtoupper(substr($p, 0, 1));
                                                    }
                                                @endphp
                                                {{ $initials ?: 'DS' }}
                                            </div>
                                            <div class="space-y-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Menunggu ACC
                                                    </span>
                                                    <span class="text-xs font-semibold text-slate-600">
                                                        {{ $courseName }} ({{ $courseCode }})
                                                    </span>
                                                </div>
                                                <h4 class="text-base font-bold text-slate-900">
                                                    {{ $module->title }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                                    <span>Pengusul: <strong class="text-slate-700">{{ $authorName }}</strong></span>
                                                    <span>•</span>
                                                    <span>Diajukan: {{ $module->created_at ? $module->created_at->diffForHumans() : '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            <a
                                                href="{{ route('kajur.modules.review', $module->id) }}"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 hover:bg-emerald-700 text-emerald-800 hover:text-white border border-emerald-300 hover:border-emerald-700 font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <span>🔍 Review Modul</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center shadow-xs">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto mb-3 border border-emerald-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Semua Pengajuan Telah Ditinjau</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Tidak ada antrian modul yang menunggu keputusan ACC dari Anda saat ini.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Action Menu Cards Kajur (4 Colorful Distinct Cards) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Card 1: Kelola Mata Kuliah (Emerald) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-emerald-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-3.5">
                                <div class="p-3 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Mata Kuliah Prodi</h4>
                                    <p class="text-xs text-slate-400">Kurikulum & SKS</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Kelola daftar mata kuliah, kode MK, bobot SKS praktikum, dan silabus program studi Anda.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('kajur.courses.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Kelola Mata Kuliah</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Review Pengajuan Modul (Amber) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-amber-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-3.5">
                                <div class="p-3 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Review Modul</h4>
                                    <p class="text-xs text-slate-400">Verifikasi LMS Builder</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Telaah draf modul, cek video, materi, dan tetapkan Master Modul atau beri catatan revisi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('kajur.modules.index', ['tab' => 'waiting']) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Buka Antrian Review</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 3: Daftar Modul Disetujui (Teal) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-teal-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-3.5">
                                <div class="p-3 bg-teal-50 text-teal-700 rounded-2xl border border-teal-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Modul Disetujui</h4>
                                    <p class="text-xs text-slate-400">Katalog resmi prodi</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Lihat arsip modul praktikum yang telah disetujui dan aktif diwariskan ke seluruh kelas praktikum prodi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('kajur.modules.index', ['tab' => 'approved']) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Katalog Arsip</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 4: Kelola Sayembara Modul (Indigo) --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-indigo-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3.5 mb-3.5">
                                <div class="p-3 bg-indigo-50 text-indigo-700 rounded-2xl border border-indigo-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">Sayembara Modul</h4>
                                    <p class="text-xs text-slate-400">Kompetisi pengusulan</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                Buka sayembara penyusunan modul baru untuk mata kuliah yang belum memiliki Master Modul resmi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('sayembaras.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                                <span>Kelola Sayembara</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role == 'dosen')
                {{-- 3. Dosen View (Dedicated template) --}}
                @include('dosen.dashboard')

            @elseif (auth()->user()->role == 'mahasiswa')
                {{-- 4. Mahasiswa View Fallback (Biasanya redirect ke student.dashboard) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-xs text-center">
                    <p class="text-sm text-slate-600">Mengarahkan ke Dasbor Mahasiswa...</p>
                    <a href="{{ route('student.dashboard') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-emerald-700 text-white font-bold text-xs uppercase rounded-xl">
                        Buka Dasbor Mahasiswa
                    </a>
                </div>
            @else
                {{-- Fallback View --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs text-slate-900">
                    <h3 class="text-lg font-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Anda telah berhasil masuk ke dalam sistem repositori modul.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
