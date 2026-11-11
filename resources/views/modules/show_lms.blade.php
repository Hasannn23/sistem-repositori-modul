<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $module->title }} - Pembaca LMS</title>

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
@endphp
<body class="h-full antialiased font-sans text-slate-800 bg-slate-50 flex flex-col overflow-hidden"
      x-data="lmsApp({{ Js::from($module->meetings) }}, {{ Js::from($module) }}, {{ Js::from($firstMeeting) }}, {{ Js::from($firstItem) }})"
      x-cloak>

    <!-- Top Navigation Bar -->
    <header class="h-16 bg-white border-b border-slate-200 shrink-0 px-4 sm:px-6 flex items-center justify-between z-20">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Toggle Sidebar Mobile/Desktop -->
            <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                    title="Toggle Daftar Pertemuan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </button>

            <!-- Module & Course Title -->
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $module->course->code ?? ($module->courseClass->name ?? 'LMS Modul') }}
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline">•</span>
                    <span class="text-xs font-medium text-slate-500 truncate hidden sm:inline">
                        {{ $module->course->name ?? ($module->courseClass->name ?? '') }}
                    </span>
                </div>
                <h1 class="text-sm sm:text-base font-bold text-slate-900 truncate">
                    {{ $module->title }}
                </h1>
            </div>
        </div>

        <!-- Right Action Items -->
        <div class="flex items-center gap-2.5 shrink-0">
            @if (auth()->check() && (auth()->id() === $module->author_id || auth()->user()->role === 'super_admin' || ($module->courseClass && $module->courseClass->dosen_id === auth()->id())))
                @if (in_array($module->status, ['draft', 'revision']) && !$module->is_master && (!$module->sayembara || $module->sayembara->status === 'open'))
                    <a href="{{ route('modules.builder', $module) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="hidden sm:inline">Edit di LMS Builder</span>
                    </a>
                @elseif ($module->status === 'approved' || $module->is_master)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-lg" title="Modul resmi prodi bersifat permanen dan terkunci dari perubahan">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="hidden sm:inline">Modul Resmi Terkunci</span>
                    </span>
                @endif
            @endif

            @if ($module->course_class_id)
                <a href="{{ route('student.class.show', $module->course_class_id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition border border-slate-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="hidden sm:inline">Kembali ke Kelas</span>
                </a>
            @else
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition border border-slate-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Two-Column Workspace Container -->
    <div class="flex-1 flex overflow-hidden relative">

        <!-- SIDEBAR KIRI: Daftar Pertemuan & Item Materi (Lebar w-80) -->
        <aside class="w-80 bg-white border-r border-slate-200 flex flex-col shrink-0 transition-all duration-300 z-10"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-0 md:overflow-hidden md:border-r-0'">
            <!-- Sidebar Header / Progress -->
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Struktur Pembelajaran</h2>
                    <span class="text-xs font-semibold text-emerald-700">{{ $module->meetings->count() }} Pertemuan Praktikum</span>
                </div>
                <button type="button" @click="expandAll = !expandAll; toggleAllMeetings()"
                        class="text-[11px] font-semibold text-slate-500 hover:text-emerald-700 transition"
                        x-text="expandAll ? 'Ciutkan Semua' : 'Buka Semua'">
                </button>
            </div>

            <!-- Accordion List Pertemuan -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @if ($module->meetings->isEmpty())
                    <div class="p-6 text-center text-xs text-slate-400">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Belum ada pertemuan LMS pada modul ini. Materi praktikum sedang disiapkan oleh Dosen pengampu.</span>
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
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $meeting->title }}</h3>
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
                            <div x-show="openMeetings.includes({{ $meeting->id }})" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
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

        <!-- CANVAS MATERI KANAN (Flex-1) -->
        <main class="flex-1 bg-slate-50 overflow-y-auto flex flex-col">
            
            <!-- Breadcrumbs / Active Context Bar -->
            <div class="px-6 sm:px-8 py-3.5 bg-white border-b border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2 truncate">
                    <span class="font-semibold text-emerald-700" x-text="activeMeeting ? 'Pertemuan ' + activeMeeting.meeting_number : 'LMS Reader'"></span>
                    <span class="text-slate-300">/</span>
                    <span class="font-medium text-slate-700 truncate" x-text="activeItem ? activeItem.title : 'Pilih Materi Pembelajaran'"></span>
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

                <!-- Author Profile Card (Reviewer View for Kajur) -->
                <x-author-profile-card :module="$module" />
                
                <!-- Blank State: Ketika belum ada item yang dipilih / belum ada meeting -->
                <template x-if="!activeItem">
                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                        <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 mb-1">Selamat Datang di Pembaca Modul LMS</h2>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                            Pilih salah satu pertemuan di sidebar kiri untuk mulai membaca materi instruksi, menonton rekaman video, atau menelaah berkas praktikum.
                        </p>
                    </div>
                </template>

                <!-- Active Content View -->
                <template x-if="activeItem">
                    <div class="space-y-6">

                        <!-- Item Header Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider"
                                      :class="{
                                          'bg-blue-50 text-blue-700 border border-blue-200': activeItem.type === 'text',
                                          'bg-rose-50 text-rose-700 border border-rose-200': activeItem.type === 'video_link',
                                          'bg-emerald-50 text-emerald-800 border border-emerald-200': activeItem.type === 'file',
                                      }">
                                    <span x-text="activeItem.type === 'text' ? 'Materi Bacaan' : (activeItem.type === 'video_link' ? 'Video Pembelajaran' : 'Berkas Lampiran')"></span>
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
                            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-10 shadow-xs">
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
                                    <span>Tautan video pembelajaran:</span>
                                    <a :href="getVideoUrl(activeItem)" target="_blank" rel="noopener noreferrer"
                                       class="font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 hover:underline">
                                        <span x-text="getVideoUrl(activeItem).includes('drive.google.com') ? 'Buka di Google Drive' : 'Buka di YouTube / Tab Baru'"></span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- 3. Tipe Item: FILE (Preview Dokumen PDF & Box Download Elegan) -->
                        <template x-if="activeItem.type === 'file'">
                            <div class="space-y-4">
                                <!-- Download Header Box -->
                                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm sm:text-base" x-text="activeItem.title"></h4>
                                            <p class="text-xs text-slate-500">Berkas pendukung materi praktikum (.pdf / dokumen)</p>
                                        </div>
                                    </div>

                                    <template x-if="activeItem.file_path">
                                        <a :href="'/storage/' + activeItem.file_path" target="_blank" download
                                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            <span>Unduh Berkas</span>
                                        </a>
                                    </template>
                                </div>

                                <!-- PDF Preview Reader Canvas (Jika berformat PDF) -->
                                <template x-if="activeItem.file_path && activeItem.file_path.toLowerCase().endsWith('.pdf')">
                                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
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
                                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
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

    <!-- Alpine.js Application Component Definition -->
    <script>
        function lmsApp(meetings, module, initialMeeting, initialItem) {
            return {
                meetings: meetings || [],
                module: module,
                sidebarOpen: true,
                expandAll: true,
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
