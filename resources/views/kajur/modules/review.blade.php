<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Review Modul: {{ $module->title }} - Evaluasi Kajur</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
@php
    $firstMeeting = $module->meetings->first();
    $firstItem = $firstMeeting?->items->first();
    $author = $module->author ?? $module->courseClass?->dosen;
    $authorName = $author?->name ?? 'Dosen Pengusul';
    $authorInitials = $author?->initials() ?? 'DS';
    $courseName = $module->course?->name ?? $module->sayembara?->course?->name ?? $module->courseClass?->course?->name ?? $module->courseClass?->name ?? 'Mata Kuliah';
    $courseCode = $module->course?->code ?? $module->sayembara?->course?->code ?? null;
@endphp
<body class="h-full antialiased font-sans text-slate-800 bg-slate-50 flex flex-col overflow-hidden"
      x-data="kajurReviewApp({{ Js::from($module->meetings) }}, {{ Js::from($module) }}, {{ Js::from($firstMeeting) }}, {{ Js::from($firstItem) }})"
      x-cloak>

    <!-- Top Sticky Header & Author Profile Card -->
    <header class="bg-white border-b border-slate-200 shrink-0 z-30 shadow-xs">
        
        <!-- Top Sub-Bar: Navigation & Context -->
        <div class="px-4 sm:px-6 py-2.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <a href="{{ route('kajur.modules.index') }}" 
                   class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 hover:text-emerald-800 hover:underline transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Antrean Pengajuan</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-500 font-medium truncate max-w-xs sm:max-w-md">
                    Review Draf Modul & Evaluasi Akademis
                </span>
            </div>

            <div class="flex items-center gap-2">
                @if ($module->status === 'waiting_review')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Review Kajur
                    </span>
                @elseif ($module->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Telah Disetujui (Master Modul)
                    </span>
                @elseif ($module->status === 'revision')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        Perlu Revisi Dosen
                    </span>
                @endif
            </div>
        </div>

        <!-- Main Header Bar: Author Profile Card & Action Bar -->
        <div class="px-4 sm:px-6 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Left Side: Author Profile Card -->
            <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                <!-- Avatar Initials -->
                <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white font-bold text-lg flex items-center justify-center shrink-0 shadow-xs border border-emerald-800">
                    {{ $authorInitials }}
                </div>

                <!-- Academic Info -->
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 truncate">
                            {{ $authorName }}
                        </h2>
                        @if ($author?->nidn)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                NIDN: {{ $author->nidn }}
                            </span>
                        @endif
                        @if ($author?->department)
                            <span class="text-xs text-emerald-700 font-semibold hidden sm:inline">
                                • {{ $author->department->name }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 flex-wrap">
                        <span class="font-semibold text-slate-800">{{ $module->title }}</span>
                        <span class="text-slate-300">•</span>
                        @if ($courseCode)
                            <span class="font-mono font-semibold text-slate-700">{{ $courseCode }} -</span>
                        @endif
                        <span class="text-slate-600 truncate">{{ $courseName }}</span>
                        <span class="text-slate-300">•</span>
                        <span>Diajukan: {{ $module->created_at->format('d M Y, H:i') }} WIB</span>
                        @if ($module->sayembara)
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1 font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200 text-[10px]">
                                🏆 Sayembara: {{ Str::limit($module->sayembara->title, 24) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Action Bar (Buttons) -->
            <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                @if ($module->sayembara && $module->sayembara->status === 'closed' && !$module->is_master && $module->status !== 'approved')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-600 font-semibold text-xs sm:text-sm rounded-xl border border-slate-200">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Sayembara Ditutup (Pemenang Sudah Ditetapkan)</span>
                    </span>
                @elseif ($module->status === 'waiting_review')
                    <!-- Kembalikan / Revisi Button -->
                    <button type="button" 
                            @click="showRejectModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 border border-rose-300 text-rose-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition duration-200">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        <span>Kembalikan/Revisi</span>
                    </button>

                    <!-- ACC & Tetapkan Master Button -->
                    <form method="POST" action="{{ route('kajur.modules.approve', $module->id) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menyetujui (ACC) modul ini dan menetapkannya sebagai MASTER MODUL resmi? Sayembara terkait akan ditutup dan materi otomatis didistribusikan ke seluruh kelas terkait.');"
                          class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ACC & Tetapkan Master</span>
                        </button>
                    </form>
                @elseif ($module->status === 'approved')
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-100 text-emerald-800 font-semibold text-xs sm:text-sm rounded-xl border border-emerald-300">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Status: Master Modul Disetujui</span>
                        </span>
                    </div>
                @elseif ($module->status === 'revision')
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showRejectModal = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs sm:text-sm rounded-xl border border-amber-300 transition">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Lihat / Perbarui Catatan Revisi</span>
                        </button>
                    </div>
                @endif
            </div>

        </div>

        @if ($module->status === 'revision' && ($module->revision_note || $module->kajur_notes))
            <div class="px-4 sm:px-6 py-2.5 bg-amber-50 border-t border-amber-200/80 text-xs text-amber-900 flex items-start gap-2">
                <span class="font-bold text-amber-800 shrink-0 uppercase tracking-wider">Catatan Evaluasi Kajur:</span>
                <span class="leading-relaxed">{{ $module->revision_note ?? $module->kajur_notes }}</span>
            </div>
        @endif
    </header>

    <!-- Two-Column LMS Reader Workspace -->
    <div class="flex-1 flex overflow-hidden relative">

        <!-- SIDEBAR KIRI: Struktur Pembelajaran & Pertemuan (w-80) -->
        <aside class="w-80 bg-white border-r border-slate-200 flex flex-col shrink-0 transition-all duration-300 z-10"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-0 md:overflow-hidden md:border-r-0'">
            
            <!-- Sidebar Header / Accordion Controls -->
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Struktur Modul</h3>
                    <span class="text-xs font-semibold text-emerald-700">{{ $module->meetings->count() }} Pertemuan Praktikum</span>
                </div>
                <button type="button" @click="expandAll = !expandAll; toggleAllMeetings()"
                        class="text-[11px] font-semibold text-slate-500 hover:text-emerald-700 transition"
                        x-text="expandAll ? 'Ciutkan Semua' : 'Buka Semua'">
                </button>
            </div>

            <!-- Accordion List Pertemuan 1-14+ -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @if ($module->meetings->isEmpty())
                    <div class="p-6 text-center text-xs text-slate-400">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Dosen belum menambahkan pertemuan pada modul ini.</span>
                    </div>
                @else
                    @foreach ($module->meetings as $meeting)
                        <div class="bg-white">
                            <!-- Accordion Header -->
                            <button type="button"
                                    @click="toggleMeeting({{ $meeting->id }})"
                                    class="w-full text-left px-4 py-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition border-l-4"
                                    :class="openMeetings.includes({{ $meeting->id }}) ? 'border-emerald-700 bg-slate-50/70' : 'border-transparent'">
                                <div class="min-w-0">
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">
                                        Pertemuan {{ $meeting->meeting_number }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $meeting->title }}</h4>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                        {{ $meeting->items->count() }} item
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                         :class="openMeetings.includes({{ $meeting->id }}) ? 'rotate-180 text-emerald-700' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            <!-- Accordion Items List -->
                            <div x-show="openMeetings.includes({{ $meeting->id }})" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0" 
                                 x-transition:enter-end="opacity-100" 
                                 x-transition:leave="transition ease-in duration-150" 
                                 x-transition:leave-start="opacity-100" 
                                 x-transition:leave-end="opacity-0">
                                <div class="bg-slate-50/60 divide-y divide-slate-100/80 py-1">
                                    @forelse ($meeting->items as $item)
                                        <button type="button"
                                                @click="selectItem({{ Js::from($item) }}, {{ Js::from($meeting) }})"
                                                class="w-full text-left px-5 py-2.5 flex items-center gap-3 transition group text-xs border-l-2"
                                                :class="activeItem && activeItem.id === {{ $item->id }} 
                                                        ? 'bg-emerald-50 text-emerald-900 border-emerald-700 font-bold' 
                                                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border-transparent'">
                                            
                                            <!-- Icon Type -->
                                            <span class="shrink-0">
                                                @if ($item->type === 'text')
                                                    <svg class="w-4 h-4" :class="activeItem && activeItem.id === {{ $item->id }} ? 'text-emerald-700' : 'text-slate-400 group-hover:text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @elseif ($item->type === 'video_link')
                                                    <svg class="w-4 h-4" :class="activeItem && activeItem.id === {{ $item->id }} ? 'text-emerald-700' : 'text-slate-400 group-hover:text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @elseif ($item->type === 'file')
                                                    <svg class="w-4 h-4" :class="activeItem && activeItem.id === {{ $item->id }} ? 'text-emerald-700' : 'text-slate-400 group-hover:text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                @endif
                                            </span>

                                            <span class="truncate flex-1">{{ $item->title }}</span>
                                        </button>
                                    @empty
                                        <div class="px-6 py-2 text-[11px] text-slate-400 italic">
                                            Belum ada materi di pertemuan ini.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </aside>

        <!-- CANVAS MATERI KANAN (Review Canvas) -->
        <main class="flex-1 bg-slate-50 overflow-y-auto flex flex-col">
            
            <!-- Breadcrumbs / Active Context Bar -->
            <div class="px-6 sm:px-8 py-3 bg-white border-b border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2 truncate">
                    <button type="button" @click="sidebarOpen = !sidebarOpen"
                            class="p-1 -ml-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded md:hidden transition mr-1"
                            title="Buka / Tutup Daftar Pertemuan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </button>
                    <span class="font-semibold text-emerald-700" x-text="activeMeeting ? 'Pertemuan ' + activeMeeting.meeting_number : 'LMS Reviewer'"></span>
                    <span class="text-slate-300">/</span>
                    <span class="font-medium text-slate-700 truncate" x-text="activeItem ? activeItem.title : 'Pilih Materi untuk Ditinjau'"></span>
                </div>

                <!-- Navigation Previous / Next Item Buttons -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="prevItem()" :disabled="!hasPrevItem()"
                            class="p-1 rounded hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Materi Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" @click="nextItem()" :disabled="!hasNextItem()"
                            class="p-1 rounded hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Materi Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 p-4 sm:p-8 max-w-5xl mx-auto w-full">
                
                <!-- Blank State: Ketika belum ada item yang dipilih / belum ada meeting -->
                <template x-if="!activeItem">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                        <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mb-4 border border-emerald-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-1">Ruang Review Draf Modul Pembelajaran</h3>
                        <p class="text-sm text-slate-600 max-w-lg mx-auto mb-6">
                            Gunakan sidebar di sebelah kiri untuk memeriksa setiap pertemuan (1 s.d. 14+), memutar video rekaman dosen, dan menelaah materi serta berkas lampiran sebelum memberikan keputusan ACC Master.
                        </p>
                    </div>
                </template>

                <!-- Active Content View -->
                <template x-if="activeItem">
                    <div class="space-y-6">

                        <!-- Item Header Card -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider"
                                      :class="{
                                          'bg-blue-50 text-blue-700 border border-blue-200': activeItem.type === 'text',
                                          'bg-rose-50 text-rose-700 border border-rose-200': activeItem.type === 'video_link',
                                          'bg-emerald-50 text-emerald-800 border border-emerald-200': activeItem.type === 'file',
                                      }">
                                    <span x-text="activeItem.type === 'text' ? 'Materi Bacaan / Instruksi' : (activeItem.type === 'video_link' ? 'Video Pembelajaran' : 'Berkas Lampiran')"></span>
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs font-semibold text-slate-500"
                                      x-text="activeMeeting ? 'Pertemuan ' + activeMeeting.meeting_number + ': ' + activeMeeting.title : ''"></span>
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight" x-text="activeItem.title"></h2>
                            
                            <!-- Meeting description if exists -->
                            <template x-if="activeMeeting && activeMeeting.description">
                                <p class="text-xs text-slate-500 mt-2 italic" x-text="activeMeeting.description"></p>
                            </template>
                        </div>

                        <!-- 1. Tipe Item: TEXT (Artikel / Instruksi bertipografi rapi prose-slate) -->
                        <template x-if="activeItem.type === 'text'">
                            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-xs">
                                <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-p:text-slate-700 prose-p:leading-relaxed prose-li:text-slate-700">
                                    <div class="whitespace-pre-line text-sm sm:text-base leading-relaxed" x-text="activeItem.content_body"></div>
                                </div>
                            </div>
                        </template>

                        <!-- 2. Tipe Item: VIDEO_LINK (Embed Responsive YouTube / Google Drive Player) -->
                        <template x-if="activeItem.type === 'video_link'">
                            <div class="space-y-4">
                                <div class="bg-slate-900 rounded-xl overflow-hidden shadow-sm aspect-video border border-slate-200/80 relative w-full">
                                    <template x-if="getYoutubeEmbedUrl(activeItem)">
                                        <iframe
                                            :src="getYoutubeEmbedUrl(activeItem)"
                                            class="absolute inset-0 w-full h-full border-0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                        </iframe>
                                    </template>
                                </div>

                                <template x-if="activeItem.content_body && !activeItem.content_body.startsWith('http')">
                                    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm">
                                        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Catatan / Ringkasan Video</h4>
                                        <div class="prose prose-slate max-w-none text-slate-600 text-sm whitespace-pre-line" x-text="activeItem.content_body"></div>
                                    </div>
                                </template>

                                <div class="bg-white rounded-xl border border-slate-200/80 p-4 flex items-center justify-between text-xs text-slate-600 shadow-sm">
                                    <span>Tautan video eksternal dosen:</span>
                                    <a :href="getVideoUrl(activeItem)" target="_blank" rel="noopener noreferrer"
                                       class="font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 hover:underline">
                                        <span x-text="getVideoUrl(activeItem).includes('drive.google.com') ? 'Buka di Google Drive' : 'Buka di YouTube / Tab Baru'"></span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- 3. Tipe Item: FILE (Preview Dokumen PDF & Box Download) -->
                        <template x-if="activeItem.type === 'file'">
                            <div class="space-y-4">
                                <!-- Download Header Box -->
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm sm:text-base" x-text="activeItem.title"></h4>
                                            <p class="text-xs text-slate-500">Berkas materi pengajaran praktikum</p>
                                        </div>
                                    </div>

                                    <template x-if="activeItem.file_path">
                                        <a :href="'/storage/' + activeItem.file_path" target="_blank" download
                                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            <span>Unduh Berkas</span>
                                        </a>
                                    </template>
                                </div>

                                <!-- PDF Preview Reader Canvas (Jika berformat PDF) -->
                                <template x-if="activeItem.file_path && activeItem.file_path.toLowerCase().endsWith('.pdf')">
                                    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                                        <div class="px-4 py-2.5 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between text-xs text-slate-600">
                                            <span class="font-medium">Pratinjau Dokumen PDF</span>
                                            <a :href="'/storage/' + activeItem.file_path" target="_blank" class="hover:text-emerald-700 hover:underline">
                                                Buka di tab penuh &rarr;
                                            </a>
                                        </div>
                                        <div class="w-full aspect-[4/3] min-h-[600px] bg-slate-800">
                                            <iframe
                                                :src="'/storage/' + activeItem.file_path"
                                                class="w-full h-full border-0"
                                                type="application/pdf">
                                            </iframe>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Bottom Navigation: Prev / Next Material Item -->
                        <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                            <button type="button" @click="prevItem()" :disabled="!hasPrevItem()"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Materi Sebelumnya</span>
                            </button>

                            <button type="button" @click="nextItem()" :disabled="!hasNextItem()"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white disabled:opacity-30 disabled:cursor-not-allowed transition shadow-xs">
                                <span>Materi Selanjutnya</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                    </div>
                </template>

            </div>
        </main>
    </div>

    <!-- Modal Catatan Revisi Kajur -->
    <div x-show="showRejectModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showRejectModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
                 @click.stop>
                
                <form method="POST" action="{{ route('kajur.modules.reject', $module->id) }}">
                    @csrf
                    
                    <!-- Modal Header -->
                    <div class="p-6 pb-4 border-b border-slate-100 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                Kembalikan Modul untuk Direvisi
                            </h3>
                            <p class="text-xs text-slate-600 mt-1">
                                Tuliskan catatan perbaikan secara spesifik agar dosen pengusul dapat menyempurnakan struktur dan materi modul.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="revision_note" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                                Catatan Perbaikan / Evaluasi <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                name="revision_note"
                                id="revision_note"
                                rows="4"
                                required
                                placeholder="Contoh: Pertemuan 3 dan 4 mohon ditambahkan video demonstrasi praktikum dan lampiran soal studi kasus."
                                class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-rose-500 focus:ring-rose-500 placeholder-slate-400"
                            >{{ old('revision_note', $module->revision_note ?? $module->kajur_notes) }}</textarea>
                            <p class="text-xs text-slate-500 mt-1">
                                Maksimal 2000 karakter. Catatan ini akan langsung terlihat oleh dosen di halaman draf modulnya.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" 
                                @click="showRejectModal = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-white transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span>Kirim Catatan & Kembalikan</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Alpine.js Application Component Definition -->
    <script>
        function kajurReviewApp(meetings, module, initialMeeting, initialItem) {
            return {
                meetings: meetings || [],
                module: module,
                sidebarOpen: true,
                expandAll: true,
                showRejectModal: false,
                openMeetings: (meetings || []).map(m => m.id),
                activeMeeting: initialMeeting || null,
                activeItem: initialItem || null,

                init() {
                    if (!this.activeItem && this.meetings.length > 0) {
                        for (let m of this.meetings) {
                            if (m.items && m.items.length > 0) {
                                this.activeMeeting = m;
                                this.activeItem = m.items[0];
                                break;
                            }
                        }
                    }
                },

                toggleMeeting(id) {
                    if (this.openMeetings.includes(id)) {
                        this.openMeetings = this.openMeetings.filter(mId => mId !== id);
                    } else {
                        this.openMeetings.push(id);
                    }
                },

                toggleAllMeetings() {
                    if (this.expandAll) {
                        this.openMeetings = this.meetings.map(m => m.id);
                    } else {
                        this.openMeetings = [];
                    }
                },

                selectItem(item, meeting) {
                    this.activeItem = item;
                    this.activeMeeting = meeting;
                    if (!this.openMeetings.includes(meeting.id)) {
                        this.openMeetings.push(meeting.id);
                    }
                },

                getAllFlatItems() {
                    let items = [];
                    for (let m of this.meetings) {
                        if (m.items) {
                            for (let it of m.items) {
                                items.push({ item: it, meeting: m });
                            }
                        }
                    }
                    return items;
                },

                hasPrevItem() {
                    if (!this.activeItem) return false;
                    const all = this.getAllFlatItems();
                    const idx = all.findIndex(x => x.item.id === this.activeItem.id);
                    return idx > 0;
                },

                hasNextItem() {
                    if (!this.activeItem) return false;
                    const all = this.getAllFlatItems();
                    const idx = all.findIndex(x => x.item.id === this.activeItem.id);
                    return idx >= 0 && idx < all.length - 1;
                },

                prevItem() {
                    const all = this.getAllFlatItems();
                    const idx = all.findIndex(x => x.item.id === this.activeItem.id);
                    if (idx > 0) {
                        this.selectItem(all[idx - 1].item, all[idx - 1].meeting);
                    }
                },

                nextItem() {
                    const all = this.getAllFlatItems();
                    const idx = all.findIndex(x => x.item.id === this.activeItem.id);
                    if (idx >= 0 && idx < all.length - 1) {
                        this.selectItem(all[idx + 1].item, all[idx + 1].meeting);
                    }
                },

                getVideoUrl(item) {
                    if (!item) return '';
                    if (typeof item === 'string') return item;
                    if (item.file_path && (item.file_path.startsWith('http://') || item.file_path.startsWith('https://'))) {
                        return item.file_path;
                    }
                    if (item.content_body && (item.content_body.startsWith('http://') || item.content_body.startsWith('https://'))) {
                        return item.content_body;
                    }
                    return item.file_path || item.content_body || '';
                },

                getYoutubeEmbedUrl(urlOrItem) {
                    const url = this.getVideoUrl(urlOrItem);
                    if (!url) return '';

                    // Google Drive Video Embed Support
                    if (url.includes('drive.google.com')) {
                        if (url.includes('/preview')) return url;
                        const driveMatch = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/) || url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                        if (driveMatch && driveMatch[1]) {
                            return 'https://drive.google.com/file/d/' + driveMatch[1] + '/preview';
                        }
                    }

                    // YouTube Video Embed Support
                    if (url.includes('youtube.com/embed/') || url.includes('youtube-nocookie.com/embed/')) return url;
                    const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|shorts\/|live\/)([^#\&\?]*).*/;
                    const match = url.match(regExp);
                    if (match && match[2] && match[2].length === 11) {
                        return 'https://www.youtube-nocookie.com/embed/' + match[2];
                    }
                    return url;
                }
            };
        }
    </script>
</body>
</html>

