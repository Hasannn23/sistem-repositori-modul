<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Dasbor Mahasiswa') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Selamat datang di Repositori & LMS Modul Praktikum Perkuliahan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Mahasiswa Aktif</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Notification Alerts --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl text-emerald-900 flex items-start gap-3 shadow-xs">
                    <div class="p-1.5 bg-emerald-600 text-white rounded-lg shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-950">Berhasil</h4>
                        <p class="text-sm mt-0.5 text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl text-rose-900 flex items-start gap-3 shadow-xs">
                    <div class="p-1.5 bg-rose-600 text-white rounded-lg shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-rose-950">Peringatan</h4>
                        <p class="text-sm mt-0.5 text-rose-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl text-rose-900 flex items-start gap-3 shadow-xs">
                    <div class="p-1.5 bg-rose-600 text-white rounded-lg shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-rose-950">Periksa Kembali Masukan Anda</h4>
                        <ul class="list-disc list-inside text-sm mt-1 text-rose-800 space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Welcome Student Profile Card (Modern Gradient Mesh) --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-7 sm:p-9 text-white shadow-md">
                <!-- Background decorative elements -->
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                        <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center text-white font-black text-2xl shadow-inner shrink-0 ring-4 ring-white/10">
                            @php
                                $nameParts = explode(' ', trim(auth()->user()->name));
                                $userInitials = '';
                                foreach (array_slice($nameParts, 0, 2) as $p) {
                                    $userInitials .= strtoupper(substr($p, 0, 1));
                                }
                            @endphp
                            {{ $userInitials ?: 'MH' }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 text-xs font-bold tracking-wider uppercase bg-emerald-500/30 border border-emerald-300/40 rounded-full text-emerald-100">
                                    NIM: {{ auth()->user()->nim ?? '-' }}
                                </span>
                                @if (auth()->user()->department)
                                    <span class="px-2.5 py-0.5 text-xs font-medium bg-white/15 border border-white/20 rounded-full text-emerald-50">
                                        {{ auth()->user()->department->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                Halo, {{ auth()->user()->name }}! 👋
                            </h3>
                            <p class="text-emerald-100/90 text-sm sm:text-base mt-1 max-w-xl">
                                Akses seluruh modul perkuliahan terstandarisasi, ikuti materi mingguan, dan pelajari praktikum di LMS resmi prodi.
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center">
                        <a href="#gabung-kelas-section" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-emerald-50 text-emerald-800 font-bold text-sm rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/80">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Gabung Kelas Baru</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 3 Colorful Metric Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                {{-- Card 1: Kelas Diikuti (Emerald) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 ring-4 ring-emerald-50/50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kelas Diikuti</p>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalClasses }}</h4>
                        <p class="text-xs text-emerald-700 font-medium mt-1">Ruang kelas praktikum aktif</p>
                    </div>
                </div>

                {{-- Card 2: Modul LMS Siap Belajar (Amber/Orange) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 ring-4 ring-amber-50/50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Modul LMS Resmi</p>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalActiveModules }}</h4>
                        <p class="text-xs text-amber-700 font-medium mt-1">Master Modul siap dipelajari</p>
                    </div>
                </div>

                {{-- Card 3: Beban SKS Praktikum (Sky/Blue) --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200 ring-4 ring-sky-50/50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Beban SKS</p>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalCredits }} <span class="text-base font-semibold text-slate-400">SKS</span></h4>
                        <p class="text-xs text-sky-700 font-medium mt-1">Total bobot SKS semester ini</p>
                    </div>
                </div>
            </div>

            {{-- 1. Form Gabung Kelas Baru (Kode Referral) --}}
            <div id="gabung-kelas-section" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-3.5 mb-2">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Gabung ke Ruang Kelas Praktikum</h3>
                            <p class="text-sm text-slate-500">Masukkan kode referral unik yang diberikan oleh Dosen Pengampu Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('student.join') }}" method="POST" class="mt-6">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1 relative">
                                <label for="referral_code" class="sr-only">Kode Referral</label>
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="referral_code"
                                    id="referral_code"
                                    value="{{ old('referral_code') }}"
                                    placeholder="Masukkan Kode Referral (Contoh: AB12CD)"
                                    required
                                    class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl uppercase tracking-widest font-mono text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-xs transition"
                                >
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-sm rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shrink-0 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Gabung Kelas Sekarang</span>
                            </button>
                        </div>
                        <p class="mt-2.5 text-xs text-slate-500 flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Kode referral terdiri dari 6 karakter acak dan hanya bisa digunakan untuk mendaftar kelas bersangkutan.</span>
                        </p>
                    </form>
                </div>
            </div>

            {{-- 2. Daftar Kelas yang Diikuti --}}
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-6 bg-emerald-700 rounded-full"></div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Kelas Praktikum Saya</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ $joinedClasses->count() }} Kelas
                        </span>
                    </div>
                </div>

                @if ($joinedClasses->isEmpty())
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto mb-4 border border-emerald-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Belum Ada Kelas yang Diikuti</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-xs mx-auto">
                            Anda belum terdaftar di kelas praktikum manapun. Silakan minta kode referral kepada dosen pengampu Anda dan gabung melalui form di atas.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($joinedClasses as $class)
                            @php
                                $dosenName = $class->dosen?->name ?? 'Dosen Belum Ditentukan';
                                $hasMaster = $class->course && $class->course->masterModule;
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-200/80 hover:border-emerald-400 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                                <div>
                                    <!-- Header Badge: Referral Code & Master Status -->
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            {{ $class->referral_code }}
                                        </span>

                                        @if ($hasMaster)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                LMS Tersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Modul Disusun
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Course & Class Name -->
                                    <h4 class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition line-clamp-2 leading-snug">
                                        {{ $class->course->name ?? $class->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1 mb-4 text-xs text-slate-500">
                                        <span class="font-medium text-slate-700">{{ $class->name }}</span>
                                        @if ($class->course)
                                            <span>•</span>
                                            <span>{{ $class->course->code }}</span>
                                            <span>•</span>
                                            <span class="font-semibold text-emerald-800">{{ $class->course->credit_hours ?? 0 }} SKS</span>
                                        @endif
                                    </div>

                                    <!-- Lecturer Info -->
                                    <div class="flex items-center gap-3 py-3 border-t border-slate-100">
                                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-emerald-200/60">
                                            @php
                                                $parts = explode(' ', trim($dosenName));
                                                $initials = '';
                                                foreach (array_slice($parts, 0, 2) as $p) {
                                                    $initials .= strtoupper(substr($p, 0, 1));
                                                }
                                            @endphp
                                            {{ $initials ?: 'DS' }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dosen Pengampu</p>
                                            <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $dosenName }}">
                                                {{ $dosenName }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button: Masuk Kelas -->
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <a
                                        href="{{ route('student.class.show', $class) }}"
                                        title="Lihat Modul"
                                        aria-label="Lihat Modul"
                                        class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-700 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        @if ($hasMaster)
                                            <span>📖 Buka LMS / Detail Kelas</span>
                                        @else
                                            <span>Masuk Kelas</span>
                                        @endif
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
