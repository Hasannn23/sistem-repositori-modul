{{-- 1. Hero / Profil Dosen Header Card --}}
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-7 sm:p-9 text-white shadow-md">
    <!-- Subtle glow backgrounds -->
    <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start sm:items-center gap-4 sm:gap-5">
            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white font-black text-2xl shadow-inner shrink-0 ring-4 ring-emerald-500/20">
                @php
                    $dosenParts = explode(' ', trim(auth()->user()->name));
                    $dosenInitials = '';
                    foreach (array_slice($dosenParts, 0, 2) as $p) {
                        $dosenInitials .= strtoupper(substr($p, 0, 1));
                    }
                @endphp
                {{ $dosenInitials ?: 'DS' }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 text-xs font-bold tracking-wider uppercase bg-emerald-500/30 border border-emerald-400/40 rounded-full text-emerald-200">
                        Dosen Pengampu
                    </span>
                    @if (auth()->user()->nidn)
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-white/10 border border-white/20 rounded-full text-slate-200">
                            NIDN: {{ auth()->user()->nidn }}
                        </span>
                    @endif
                    <span class="px-2.5 py-0.5 text-xs font-medium bg-white/10 border border-white/20 rounded-full text-slate-300">
                        Jurusan: {{ auth()->user()->department->name ?? 'Belum Ditentukan' }}
                    </span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Selamat Datang, {{ auth()->user()->name }}! 👨‍🏫
                </h3>
                <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
                    Kelola ruang kelas praktikum, bagikan kode referral ke mahasiswa, serta susun modul interaktif melalui Sayembara Prodi.
                </p>
            </div>
        </div>

        <div class="shrink-0 flex flex-wrap items-center gap-3">
            <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Kelola Kelas</span>
            </a>
            <a href="{{ route('sayembaras.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Sayembara Modul</span>
            </a>
        </div>
    </div>
</div>

{{-- 2. Grid 4 Metrik Statistik Dosen --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
    {{-- Card 1: Kelas Diampu (Sky/Blue) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
        <div class="w-13 h-13 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200 ring-4 ring-sky-50/50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kelas Diampu</p>
            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total_classes'] ?? 0 }}</h4>
            <p class="text-xs text-sky-700 font-medium mt-1">Ruang kelas praktikum aktif</p>
        </div>
    </div>

    {{-- Card 2: Mahasiswa Terdaftar (Emerald) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
        <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 ring-4 ring-emerald-50/50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Mahasiswa</p>
            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total_students'] ?? 0 }}</h4>
            <p class="text-xs text-emerald-700 font-medium mt-1">Terdaftar di seluruh kelas</p>
        </div>
    </div>

    {{-- Card 3: Modul Disusun (Purple/Violet) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
        <div class="w-13 h-13 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-200 ring-4 ring-purple-50/50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Modul LMS Dosen</p>
            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total_modules'] ?? 0 }}</h4>
            <p class="text-xs text-purple-700 font-medium mt-1">
                {{ $stats['draft_modules_count'] ?? 0 }} draf • {{ $stats['approved_modules_count'] ?? 0 }} ACC
            </p>
        </div>
    </div>

    {{-- Card 4: Sayembara Terbuka (Amber) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
        <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 ring-4 ring-amber-50/50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sayembara Terbuka</p>
            <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $stats['open_sayembaras_count'] ?? 0 }}</h4>
            <p class="text-xs text-amber-700 font-medium mt-1">Peluang Master Modul Prodi</p>
        </div>
    </div>
</div>

{{-- 3. Banner / Card Pengumuman Sayembara Penyusunan Modul Terbuka --}}
@if (isset($openSayembaras) && $openSayembaras->isNotEmpty())
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-6 bg-amber-500 rounded-full"></div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Sayembara Terbuka Hari Ini</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    {{ $openSayembaras->count() }} Terbuka
                </span>
            </div>
            <a href="{{ route('sayembaras.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 uppercase tracking-wider transition flex items-center gap-1">
                <span>Lihat Semua</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @foreach ($openSayembaras as $sayembara)
            <div class="bg-gradient-to-br from-emerald-50/90 to-teal-50/50 border border-emerald-300/80 rounded-2xl p-6 shadow-xs transition hover:shadow-md">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-emerald-600 text-white rounded-2xl shrink-0 mt-0.5 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-700 text-white tracking-wider uppercase">
                                    Pengumuman
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    Sayembara Penyusunan Modul Terbuka
                                </span>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900 mt-1">
                                {{ $sayembara->title }}
                            </h4>
                            <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-slate-700">
                                <div class="flex items-center gap-1.5 font-semibold text-emerald-900">
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span>Mata Kuliah: <strong class="text-slate-900">{{ $sayembara->course->name ?? 'Mata Kuliah' }}</strong> ({{ $sayembara->course->code ?? '-' }})</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Batas Pengajuan: <strong class="text-slate-900">{{ $sayembara->end_date ? $sayembara->end_date->format('d M Y') : 'Selesai' }}</strong></span>
                                </div>
                            </div>

                            {{-- Live Participant & Social Proof Avatar Group --}}
                            <div class="mt-3.5 pt-3 border-t border-emerald-200/60 flex items-center">
                                <x-sayembara-participants :sayembara="$sayembara" variant="dosen" />
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('sayembaras.show', $sayembara) }}" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-sm rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <span>Ikuti Sayembara / Susun Modul</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- 4. Modul yang Sedang Disusun (Draf & Perlu Revisi) --}}
@if (isset($stats['draft_modules']) && $stats['draft_modules']->isNotEmpty())
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-6 bg-purple-600 rounded-full"></div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Draf Modul LMS yang Sedang Disusun</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    {{ $stats['draft_modules']->count() }} Modul
                </span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="divide-y divide-slate-100">
                @foreach ($stats['draft_modules'] as $module)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($module->status === 'revision')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draf LMS
                                    </span>
                                @endif
                                <span class="text-xs font-medium text-slate-500">
                                    {{ $module->course->name ?? $module->sayembara->course->name ?? 'Mata Kuliah' }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900">
                                {{ $module->title }}
                            </h4>
                            @if ($module->status === 'revision' && $module->revision_note)
                                <div class="mt-2 p-2.5 bg-rose-50 border-l-3 border-rose-500 rounded-r-lg text-xs text-rose-800">
                                    <strong>Catatan Kajur:</strong> {{ $module->revision_note }}
                                </div>
                            @endif
                        </div>

                        <div class="shrink-0">
                            <a
                                href="{{ route('dosen.modules.builder', $module->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Lanjut di LMS Builder</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- 5. Dua Kartu Fitur Aksi Cepat (Quick Actions Grid) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    {{-- Card 1: Kelola & Buat Kelas Praktikum --}}
    <div class="bg-white overflow-hidden shadow-xs rounded-2xl p-6 sm:p-7 flex flex-col justify-between border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition">
        <div>
            <div class="flex items-center space-x-3.5 mb-3.5">
                <div class="p-3 bg-sky-50 text-sky-700 rounded-2xl border border-sky-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-900">Kelola Ruang Kelas</h4>
                    <p class="text-xs text-slate-400">Pemberian kode referral & presensi praktikan</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                Buka kelas perkuliahan baru, bagikan kode referral kepada mahasiswa, dan otomatis tautkan master modul kurikulum prodi.
            </p>
        </div>
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg">
                {{ $stats['total_classes'] ?? 0 }} Kelas Aktif
            </span>
            <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                <span>Buka Ruang Kelas</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Card 2: Sayembara Modul --}}
    <div class="bg-white overflow-hidden shadow-xs rounded-2xl p-6 sm:p-7 flex flex-col justify-between border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition">
        <div>
            <div class="flex items-center space-x-3.5 mb-3.5">
                <div class="p-3 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-900">Sayembara Penyusunan Modul</h4>
                    <p class="text-xs text-slate-400">Kompetisi modul resmi mata kuliah prodi</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                Susun modul interaktif berbasis pertemuan menggunakan LMS Builder dan ajukan ke Kepala Jurusan untuk ditetapkan sebagai Master Modul resmi.
            </p>
        </div>
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                {{ $stats['open_sayembaras_count'] ?? 0 }} Sayembara Terbuka
            </span>
            <a href="{{ route('sayembaras.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-xs">
                <span>Katalog Sayembara</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
