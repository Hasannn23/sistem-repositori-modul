<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                    title="Kembali ke Dasbor"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                            Kelas Mahasiswa
                        </span>
                        @if ($courseClass->course?->code)
                            <span class="text-xs font-mono font-semibold text-slate-500">
                                {{ $courseClass->course->code }}
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                        {{ $courseClass->course->name ?? $courseClass->name }}
                    </h2>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500">
                        {{ $courseClass->name }} &bull; Kode Kelas: <span class="font-mono font-semibold text-emerald-700">{{ $courseClass->referral_code }}</span>
                    </p>
                </div>
            </div>
            <div>
                <a
                    href="{{ route('student.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl shadow-2xs transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Kelas Saya</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Informasi Ruang Kelas (Clean Anti-Slop Card) --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0 border border-emerald-200/60 shadow-xs">
                            @php
                                $dosenName = $courseClass->dosen?->name ?? 'Dosen Belum Ditentukan';
                                $nameParts = explode(' ', trim($dosenName));
                                $initials = '';
                                foreach (array_slice($nameParts, 0, 2) as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                            @endphp
                            {{ $initials ?: 'DS' }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Kode: {{ $courseClass->referral_code }}
                                </span>
                                @if ($courseClass->course?->credit_hours)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $courseClass->course->credit_hours }} SKS
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900">
                                {{ $courseClass->course->name ?? $courseClass->name }}
                            </h3>
                            <p class="text-sm font-semibold text-emerald-700 mt-0.5">
                                {{ $courseClass->name }}
                            </p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs sm:text-sm text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-medium text-slate-800">Dosen Pengampu:</span> {{ $dosenName }}
                                </span>
                                @if ($courseClass->dosen?->department)
                                    <span class="text-slate-300">&bull;</span>
                                    <span>Jurusan: {{ $courseClass->dosen->department->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Area Modul Praktikum Resmi (Inheritansi Master Modul) --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Modul Praktikum Resmi</h3>
                        <p class="text-xs sm:text-sm text-slate-500">Materi kurikulum interaktif yang telah disahkan oleh Program Studi.</p>
                    </div>
                    @if ($masterModule)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Modul Aktif
                        </span>
                    @endif
                </div>

                @if ($masterModule)
                    {{-- Card Master Modul & Big Prominent CTA --}}
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition hover:shadow-md">
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                
                                <div class="space-y-3 max-w-2xl">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-extrabold bg-amber-500 text-white shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            MASTER MODUL RESMI PRODI
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            LMS Interaktif
                                        </span>
                                    </div>

                                    <h4 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                                        {{ $masterModule->title }}
                                    </h4>

                                    @if ($masterModule->description)
                                        <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                                            {{ $masterModule->description }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-xs text-slate-500">
                                        <span class="flex items-center gap-1.5 text-emerald-800 font-semibold">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>{{ $masterModule->meetings->count() }} Pertemuan Praktikum</span>
                                        </span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>Penyusun: <strong class="text-slate-700">{{ $masterModule->authorUser?->name ?? $masterModule->author?->name ?? 'Tim Dosen' }}</strong></span>
                                    </div>
                                </div>

                                {{-- Single Big Prominent CTA Emerald-700 --}}
                                <div class="shrink-0 flex items-center">
                                    <a
                                        href="{{ route('modules.lms', $masterModule->id) }}"
                                        title="Lihat Modul"
                                        aria-label="Lihat Modul"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-base sm:text-lg rounded-xl shadow-sm hover:shadow-md transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        <span class="sr-only">Lihat Modul</span>
                                        <svg class="w-6 h-6 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span>📖 Buka LMS / Mulai Belajar</span>
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State Estetik dengan Border Dashed Slate-300 --}}
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center bg-white/60 max-w-2xl mx-auto shadow-2xs">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-4 border border-slate-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">
                            Modul Belum Ditetapkan
                        </h4>
                        <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto leading-relaxed">
                            Modul praktikum untuk mata kuliah ini sedang dalam penyusunan oleh Program Studi.
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            Materi pembelajaran akan otomatis muncul di halaman ini setelah disahkan oleh Kepala Jurusan.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
