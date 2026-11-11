<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-xs text-gray-300">/</span>
                    <span class="text-xs text-gray-500 font-medium">Review Pengajuan Modul</span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Review & Persetujuan Modul Praktikum
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola dan periksa modul praktikum yang diajukan oleh dosen di lingkungan <span class="font-semibold text-gray-800">{{ auth()->user()->department->name ?? 'Jurusan Anda' }}</span>.
                </p>
            </div>

            <!-- Antrean Badge -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 text-blue-800 font-semibold text-sm rounded-xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>{{ $waitingCount }} Modul Menunggu ACC</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{
        selectedModuleTitle: '',
        rejectActionUrl: '',
        openRejectModal(title, actionUrl) {
            this.selectedModuleTitle = title;
            this.rejectActionUrl = actionUrl;
            $dispatch('open-modal', 'reject-module-modal');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs transition">
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
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex items-start gap-3 shadow-xs transition">
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

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 shadow-xs">
                    <p class="text-sm font-semibold mb-1">Terdapat kesalahan pada input Anda:</p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tab Switcher & Search Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <!-- Tabs -->
                <div class="flex items-center gap-2 p-1 bg-gray-100 rounded-xl max-w-fit">
                    <a href="{{ route('kajur.modules.index', ['tab' => 'waiting']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition {{ $tab !== 'approved' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                        <span>Menunggu Review</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tab !== 'approved' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $waitingCount }}
                        </span>
                    </a>

                    <a href="{{ route('kajur.modules.index', ['tab' => 'approved']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition {{ $tab === 'approved' ? 'bg-white text-emerald-800 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                        <span>Daftar Modul Disetujui</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $approvedCount }}
                        </span>
                    </a>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('kajur.modules.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari judul, kelas, dosen..." 
                            class="text-xs sm:text-sm rounded-xl border-gray-300 py-2 pl-9 pr-3 focus:border-blue-500 focus:ring-blue-500 placeholder-gray-400 w-full sm:w-64"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('kajur.modules.index', ['tab' => $tab]) }}" class="p-2 text-xs text-gray-400 hover:text-gray-600 rounded-lg" title="Reset">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            <!-- Konten Utama Berdasarkan Tab -->
            @if ($modules->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if ($tab === 'approved')
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Modul yang Disetujui</h3>
                        <p class="text-sm text-gray-500 max-w-md mx-auto">
                            Saat ini belum ada modul praktikum yang telah disetujui (ACC) di lingkungan jurusan Anda.
                        </p>
                    @else
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Semua Modul Telah Ditinjau</h3>
                        <p class="text-sm text-gray-500 max-w-md mx-auto">
                            Saat ini tidak ada draf modul praktikum yang sedang menunggu persetujuan (ACC) dari dosen di jurusan Anda.
                        </p>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-base text-gray-900">
                                @if ($tab === 'approved')
                                    Arsip Modul Praktikum Disetujui ({{ $modules->total() }})
                                @else
                                    Antrean Pengajuan Modul ({{ $modules->total() }})
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if ($tab === 'approved')
                                    Daftar modul yang telah divalidasi dan dapat diakses mahasiswa secara publik.
                                @else
                                    Periksa berkas atau tautan modul, lalu berikan persetujuan atau catatan evaluasi revisi.
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            {{ $tab === 'approved' ? '● Disetujui (ACC)' : '● Menunggu Review' }}
                        </span>
                    </div>

                    <div class="overflow-hidden">
                        {{-- Mobile Card View --}}
                        <div class="block lg:hidden divide-y divide-gray-100">
                            @foreach ($modules as $module)
                                @php
                                    $isLink = in_array($module->storage_type, ['link', 'gdrive']) || str_starts_with($module->file_path ?? '', 'http');
                                    $author = $module->author ?? $module->courseClass?->dosen;
                                    $authorName = $author?->name ?? 'Dosen Pengusul';
                                    $courseName = $module->course?->name ?? $module->sayembara?->course?->name ?? $module->courseClass?->course?->name ?? $module->courseClass?->name ?? '-';
                                    $courseCode = $module->course?->code ?? $module->sayembara?->course?->code ?? null;
                                @endphp
                                <div class="p-4 hover:bg-gray-50/60 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-900 block leading-tight text-sm">{{ $module->title }}</span>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="text-[11px] font-medium text-slate-700">{{ $authorName }}</span>
                                                    @if ($author?->nidn)
                                                        <span class="text-[10px] text-slate-400 font-mono">({{ $author->nidn }})</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 mt-1 flex flex-wrap items-center gap-1.5">
                                                    @if ($courseCode)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                                            {{ $courseCode }}
                                                        </span>
                                                    @endif
                                                    <span class="font-medium text-slate-800">{{ $courseName }}</span>
                                                    <span>&bull;</span>
                                                    <span class="text-slate-400">{{ $module->updated_at->format('d M Y') }}</span>
                                                </div>
                                                @if ($module->sayembara)
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                            <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                            <span>Sayembara: {{ $module->sayembara->title }}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="shrink-0 pt-1 flex flex-col gap-1.5 items-end">
                                            @if ($module->sayembara_id)
                                                <a href="{{ route('sayembaras.show', $module->sayembara_id) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold transition shadow-2xs whitespace-nowrap">
                                                    <span>🏆 Buka Sayembara</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('kajur.modules.review', $module->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-600 hover:border-emerald-700 text-emerald-700 hover:bg-emerald-50 text-xs font-semibold transition shadow-2xs whitespace-nowrap">
                                                <span>🔍 Review Modul</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desktop Table View --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/70 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Judul Modul</th>
                                        <th class="py-3.5 px-6">Dosen Pengusul</th>
                                        <th class="py-3.5 px-6">Mata Kuliah & Sayembara</th>
                                        <th class="py-3.5 px-6">
                                            {{ $tab === 'approved' ? 'Tanggal Disetujui' : 'Tanggal Diajukan' }}
                                        </th>
                                        <th class="py-3.5 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach ($modules as $module)
                                        @php
                                            $isLink = in_array($module->storage_type, ['link', 'gdrive']) || str_starts_with($module->file_path ?? '', 'http');
                                            $author = $module->author ?? $module->courseClass?->dosen;
                                            $authorName = $author?->name ?? 'Dosen Tidak Diketahui';
                                            $authorInitials = $author?->initials() ?? 'DS';
                                            $courseName = $module->course?->name ?? $module->sayembara?->course?->name ?? $module->courseClass?->course?->name ?? $module->courseClass?->name ?? '-';
                                            $courseCode = $module->course?->code ?? $module->sayembara?->course?->code ?? null;
                                        @endphp
                                        <tr class="hover:bg-gray-50/60 transition-colors">
                                            <!-- Judul Modul -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span class="font-bold text-gray-900 block leading-tight">
                                                            {{ $module->title }}
                                                        </span>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $module->storage_type === 'lms' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                                                {{ strtoupper($module->storage_type ?? 'LMS') }}
                                                            </span>
                                                            <span class="text-xs text-slate-500">
                                                                {{ $module->meetings->count() }} Pertemuan
                                                            </span>
                                                        </div>
                                                        @if ($module->kajur_notes && $tab !== 'approved')
                                                            <div class="mt-1.5 text-xs text-amber-700 bg-amber-50 p-1.5 rounded border border-amber-200">
                                                                <strong class="font-semibold">Catatan Sebelumnya:</strong> {{ Str::limit($module->kajur_notes, 80) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Dosen Pengusul -->
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0">
                                                        {{ $authorInitials }}
                                                    </div>
                                                    <div>
                                                        <span class="font-semibold text-gray-900 block leading-tight">
                                                            {{ $authorName }}
                                                        </span>
                                                        @if ($author?->nidn)
                                                            <span class="text-xs text-slate-500 font-mono block">
                                                                NIDN: {{ $author->nidn }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-slate-500 block">
                                                                {{ $author?->email ?? '-' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Mata Kuliah & Sayembara -->
                                            <td class="py-4 px-6">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        @if ($courseCode)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                                                {{ $courseCode }}
                                                            </span>
                                                        @endif
                                                        <span class="font-medium text-gray-800 block truncate">
                                                            {{ $courseName }}
                                                        </span>
                                                    </div>
                                                    @if ($module->sayembara)
                                                        <div class="mt-1 flex items-center gap-1">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                                <span class="truncate max-w-xs">{{ Str::limit($module->sayembara->title, 32) }}</span>
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-slate-400 block mt-0.5">Non-Sayembara</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Tanggal -->
                                            <td class="py-4 px-6 text-xs text-gray-500 whitespace-nowrap">
                                                {{ $module->updated_at->format('d M Y, H:i') }}
                                            </td>

                                            <!-- Aksi Review Modul -->
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center justify-end gap-2">
                                                    @if ($module->sayembara_id)
                                                        <a href="{{ route('sayembaras.show', $module->sayembara_id) }}"
                                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold transition-colors duration-200 shadow-sm"
                                                           title="Lihat perbandingan kontestan di Detail Sayembara">
                                                            <span>🏆 Buka Sayembara</span>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('kajur.modules.review', $module->id) }}"
                                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-600 hover:border-emerald-700 text-emerald-700 hover:bg-emerald-50 text-xs font-semibold transition-colors duration-200 shadow-2xs">
                                                        <span>🔍 Review Modul</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $modules->links() }}
                    </div>
                </div>
            @endif

        </div>

        <!-- Modal Alasan Revisi Kajur -->
        <x-modal name="reject-module-modal" focusable maxWidth="md">
            <form method="POST" :action="rejectActionUrl" class="p-6">
                @csrf

                <!-- Header Modal -->
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Kembalikan Modul (Revisi)
                        </h3>
                        <p class="text-xs text-gray-500">
                            Berikan catatan evaluasi agar dosen pengampu dapat memperbaiki modul praktikum.
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Modul yang ditolak:</span>
                        <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedModuleTitle"></p>
                    </div>

                    <div>
                        <label for="kajur_notes" class="block text-sm font-semibold text-gray-700 mb-1">
                            Catatan Revisi / Alasan Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="kajur_notes"
                            id="kajur_notes"
                            rows="4"
                            required
                            placeholder="Tuliskan bagian mana yang perlu diperbaiki atau dilengkapi oleh dosen pengampu..."
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-red-500 focus:ring-red-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400"></textarea>
                        @error('kajur_notes')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="mt-6 pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Kirim Catatan Revisi</span>
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
