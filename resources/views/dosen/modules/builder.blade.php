<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $module->title }} - LMS Module Builder | {{ config('app.name', 'Sistem Repositori Modul') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50"
      x-data="lmsBuilder({
          autosaveUrl: '{{ route('modules.builder.autosave', $module) }}',
          initialTitle: @js($module->title),
          csrfToken: '{{ csrf_token() }}'
      })">

    <!-- STICKY TOPBAR (FULL-SCREEN DISTRACTION-FREE WORKSPACE HEADER) -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 py-3 shadow-2xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            
            <!-- Left: Back, Course Meta & Inline Title Edit -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ $module->sayembara_id ? route('sayembaras.show', $module->sayembara_id) : route('modules.index') }}"
                   class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition shrink-0"
                   title="Keluar dari Ruang Kerja Builder">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                            {{ $module->course->code ?? 'LMS' }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium truncate">
                            {{ $module->course->name ?? 'Kurikulum Pembelajaran' }}
                        </span>
                        <!-- Status Badge "Draft" -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ ucfirst($module->status ?? 'Draft') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <input type="text"
                               x-model="moduleTitle"
                               @input.debounce.1000ms="silentSave()"
                               class="text-base sm:text-lg font-bold text-slate-900 bg-transparent border-0 border-b border-transparent hover:border-slate-300 focus:border-emerald-500 focus:ring-0 p-0 w-full truncate cursor-pointer focus:cursor-text"
                               placeholder="Ketikkan Judul Modul Pembelajaran...">
                    </div>
                </div>
            </div>

            <!-- Right: Status & Action Buttons -->
            <div class="flex items-center justify-between md:justify-end gap-2.5 shrink-0">
                
                <!-- Autosave Live Indicator -->
                <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                    <template x-if="isSaving">
                        <span class="flex items-center gap-1.5 text-amber-700 font-medium">
                            <svg class="w-3.5 h-3.5 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </template>
                    <template x-if="!isSaving">
                        <span class="flex items-center gap-1.5 text-emerald-700 font-medium" x-text="saveStatus">
                            📝 Draft Tersimpan
                        </span>
                    </template>
                </div>

                <!-- Tombol Outline: Simpan Progres -->
                <button type="button"
                        @click="silentSave(true)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Simpan Progres</span>
                </button>

                <!-- Preview LMS Reader -->
                <a href="{{ route('modules.lms', $module) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="hidden sm:inline">Pratinjau</span>
                </a>

                <!-- Tombol Utama Emerald: Ajukan ke Kajur -->
                <button type="button"
                        @click="openModal('submit-kajur')"
                        class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl transition-colors duration-200 shadow-sm">
                    <span>Ajukan ke Kajur</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.closest('div').remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.closest('div').remove()" class="text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
            </div>
        @endif
    </div>

    <!-- MAIN WORKSPACE CANVAS -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        
        <!-- Canvas Header Banner -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Kanvas Penyusunan Modul LMS
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Susun kurikulum pertemuan demi pertemuan (1 s.d. 14+), lalu sisipkan materi teks, embed video YouTube, atau berkas pendukung.
                    </p>
                </div>

                <!-- Tombol Besar: + Tambah Pertemuan -->
                <form method="POST" action="{{ route('modules.builder.meetings.store', $module) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Tambah Pertemuan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ACCORDION PERTEMUAN LIST -->
        @if ($module->meetings->isEmpty())
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mb-4 border border-emerald-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Pertemuan yang Dibuat</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-1 mb-6">
                    Mulailah dengan menambahkan Pertemuan 1 untuk mengorganisir kurikulum materi kuliah dan praktikum.
                </p>
                <form method="POST" action="{{ route('modules.builder.meetings.store', $module) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Pertemuan Pertama</span>
                    </button>
                </form>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($module->meetings as $meeting)
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden transition"
                         x-data="{ isOpen: true, dropdownOpen: false }">
                        
                        <!-- Accordion Header -->
                        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between gap-4 cursor-pointer select-none"
                             @click="isOpen = !isOpen">
                            
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold shrink-0 font-mono">
                                    {{ $meeting->meeting_number }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-slate-900 truncate">
                                        {{ $meeting->title }}
                                    </h3>
                                    @if ($meeting->description)
                                        <p class="text-xs text-slate-500 truncate max-w-xl">
                                            {{ $meeting->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0" @click.stop>
                                <span class="text-xs text-slate-500 font-medium px-2 py-1 bg-white rounded-lg border border-slate-200">
                                    {{ $meeting->items->count() }} Materi
                                </span>

                                <!-- Dropdown: + Tambah Materi (Pilihan Teks, Video YouTube, Upload File) -->
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button"
                                            @click="open = !open"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-lg border border-emerald-200 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>+ Tambah Materi</span>
                                        <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl border border-slate-200 shadow-lg py-1.5 z-20">
                                        
                                        <button type="button"
                                                @click="openAddItemModal({{ $meeting->id }}, '{{ addslashes($meeting->title) }}', 'text'); open = false;"
                                                class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-900 flex items-center gap-2.5 transition-colors">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </span>
                                            <span>Tambah Teks / Instruksi</span>
                                        </button>

                                        <button type="button"
                                                @click="openAddItemModal({{ $meeting->id }}, '{{ addslashes($meeting->title) }}', 'video_link'); open = false;"
                                                class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-red-50 hover:text-red-900 flex items-center gap-2.5 transition-colors">
                                            <span class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                            <span>Tambah Video YouTube</span>
                                        </button>

                                        <button type="button"
                                                @click="openAddItemModal({{ $meeting->id }}, '{{ addslashes($meeting->title) }}', 'file'); open = false;"
                                                class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-900 flex items-center gap-2.5 transition-colors">
                                            <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                            <span>Upload File (PDF/PPT)</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Delete Meeting Form -->
                                <form method="POST" action="{{ route('modules.builder.meetings.destroy', $meeting) }}"
                                      onsubmit="return confirm('Hapus {{ $meeting->title }} beserta seluruh materi di dalamnya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Pertemuan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Accordion Chevron Toggle -->
                                <button type="button" @click="isOpen = !isOpen" class="p-1.5 text-slate-400 hover:text-slate-600 transition">
                                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Accordion Content: Items List -->
                        <div x-show="isOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="p-5 space-y-3">
                            
                            @if ($meeting->items->isEmpty())
                                <div class="py-6 text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                    <p class="text-xs text-slate-500">
                                        Belum ada materi pembelajaran pada pertemuan ini.
                                    </p>
                                    <button type="button"
                                            @click="openAddItemModal({{ $meeting->id }}, '{{ addslashes($meeting->title) }}', 'text')"
                                            class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                        <span>+ Sisipkan Materi Pertama</span>
                                    </button>
                                </div>
                            @else
                                <div class="space-y-2.5">
                                    @foreach ($meeting->items as $item)
                                        <div class="flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100/70 rounded-xl border border-slate-200/70 transition-colors">
                                            
                                            <!-- Item Type Icon & Info -->
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if ($item->type === 'text')
                                                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                @elseif ($item->type === 'video_link')
                                                    <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif

                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded {{ $item->type === 'text' ? 'bg-emerald-100 text-emerald-800' : ($item->type === 'video_link' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                            {{ $item->type === 'text' ? 'Teks Instruksi' : ($item->type === 'video_link' ? 'Video YouTube' : 'Berkas Lampiran') }}
                                                        </span>
                                                        <h4 class="text-sm font-semibold text-slate-800 truncate">
                                                            {{ $item->title }}
                                                        </h4>
                                                    </div>
                                                    @if ($item->content_body)
                                                        <p class="text-xs text-slate-500 truncate max-w-lg mt-0.5">
                                                            {{ Str::limit(strip_tags($item->content_body), 80) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Actions: Delete Item -->
                                            <div class="flex items-center gap-2 shrink-0">
                                                <form method="POST" action="{{ route('modules.builder.items.destroy', $item) }}"
                                                      onsubmit="return confirm('Hapus materi pembelajaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Materi">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- MODAL: TAMBAH MATERI PEMBELAJARAN (TEXT, VIDEO, FILE) -->
    <div x-show="activeModal === 'add-item'"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full p-6 overflow-hidden"
             @click.away="activeModal = null">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Sisipkan Materi Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="targetMeetingTitle"></p>
                </div>
                <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-lg font-bold">
                    &times;
                </button>
            </div>

            <!-- Material Type Selector Tabs -->
            <div x-data="{ tab: activeTab }">
                <div class="flex p-1 mt-4 bg-slate-100 rounded-xl gap-1">
                    <button type="button"
                            @click="tab = 'text'"
                            :class="{ 'bg-white text-emerald-800 shadow-xs font-bold': tab === 'text', 'text-slate-600': tab !== 'text' }"
                            class="flex-1 py-2 text-xs font-semibold rounded-lg transition text-center">
                        📝 Teks / Instruksi
                    </button>
                    <button type="button"
                            @click="tab = 'video_link'"
                            :class="{ 'bg-white text-red-800 shadow-xs font-bold': tab === 'video_link', 'text-slate-600': tab !== 'video_link' }"
                            class="flex-1 py-2 text-xs font-semibold rounded-lg transition text-center">
                        🎬 Video (YouTube / Drive)
                    </button>
                    <button type="button"
                            @click="tab = 'file'"
                            :class="{ 'bg-white text-blue-800 shadow-xs font-bold': tab === 'file', 'text-slate-600': tab !== 'file' }"
                            class="flex-1 py-2 text-xs font-semibold rounded-lg transition text-center">
                        📎 Lampiran Berkas (PDF/PPT)
                    </button>
                </div>

                <!-- TAB 1: FORM TEKS -->
                <div x-show="tab === 'text'" class="pt-5">
                    <form method="POST" :action="addItemFormUrl">
                        @csrf
                        <input type="hidden" name="type" value="text">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Judul Materi Teks <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required placeholder="Contoh: Pengantar Konsep Relasi Database"
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Konten / Instruksi Perkuliahan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="content_body" rows="6" required placeholder="Tuliskan catatan, penjelasan konsep, atau instruksi langkah praktikum di sini..."
                                          class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5"></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="activeModal = null" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                    Simpan Materi Teks
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: FORM VIDEO LINK -->
                <div x-show="tab === 'video_link'" class="pt-5">
                    <form method="POST" :action="addItemFormUrl">
                        @csrf
                        <input type="hidden" name="type" value="video_link">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Judul Video Pembelajaran <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required placeholder="Contoh: Video Penjelasan Normalisasi Data"
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Tautan Video (YouTube atau Google Drive) <span class="text-red-500">*</span>
                                </label>
                                <input type="url" name="video_url" required placeholder="https://www.youtube.com/watch?v=... atau https://drive.google.com/file/d/..."
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5">
                                <p class="mt-1.5 text-[11px] text-slate-500 leading-relaxed">
                                    Mendukung link YouTube maupun video Google Drive (pastikan akses file Drive diatur ke <span class="font-semibold text-slate-700">"Siapa saja yang memiliki link"</span>).
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Catatan / Ringkasan Video (Opsional)
                                </label>
                                <textarea name="content_body" rows="3" placeholder="Poin-poin penting yang perlu diperhatikan saat menonton..."
                                          class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5"></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="activeModal = null" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                    Simpan Video
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: FORM FILE ATTACHMENT -->
                <div x-show="tab === 'file'" class="pt-5">
                    <form method="POST" :action="addItemFormUrl" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="file">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Judul Berkas / Bahan Kuliah <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required placeholder="Contoh: Slide Kuliah PPT Pertemuan 1"
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Pilih Berkas (PDF, PPT, PPTX, DOCX, ZIP - Max 10MB) <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" required accept=".pdf,.ppt,.pptx,.docx,.zip"
                                       class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Keterangan Berkas (Opsional)
                                </label>
                                <textarea name="content_body" rows="2" placeholder="Petunjuk pengerjaan berkas pendukung..."
                                          class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5"></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="activeModal = null" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                    Unggah Berkas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: AJUKAN KE KAJUR -->
    <div x-show="activeModal === 'submit-kajur'"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6"
             @click.away="activeModal = null">
            
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4 border border-emerald-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h3 class="text-lg font-bold text-slate-900">Ajukan Modul ke Kajur?</h3>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                Setelah diajukan, modul akan <strong>dikunci sementara (read-only)</strong> agar Kepala Jurusan dapat mereview materi yang telah Anda susun secara utuh.
            </p>

            <form method="POST" action="{{ route('modules.builder.submit', $module) }}" class="mt-6 flex items-center justify-end gap-2">
                @csrf
                <button type="button" @click="activeModal = null"
                        class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition">
                    Ya, Ajukan Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- ALPINE LOGIC HELPER -->
    <script>
        function lmsBuilder(config) {
            return {
                moduleTitle: config.initialTitle,
                saveStatus: '📝 Draft Tersimpan',
                isSaving: false,
                activeModal: null,
                targetMeetingId: null,
                targetMeetingTitle: '',
                activeTab: 'text',
                addItemFormUrl: '',

                openModal(name) {
                    this.activeModal = name;
                },

                openAddItemModal(meetingId, title, initialTab = 'text') {
                    this.targetMeetingId = meetingId;
                    this.targetMeetingTitle = title;
                    this.activeTab = initialTab;
                    this.addItemFormUrl = '/builder/meetings/' + meetingId + '/items';
                    this.activeModal = 'add-item';
                },

                async silentSave(explicit = false) {
                    this.isSaving = true;
                    try {
                        const response = await fetch(config.autosaveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                title: this.moduleTitle,
                            }),
                        });

                        const data = await response.json();
                        if (response.ok && data.status === 'success') {
                            this.saveStatus = '✓ Tersimpan ' + (data.saved_at || '');
                        } else {
                            this.saveStatus = '⚠️ Gagal menyimpan';
                        }
                    } catch (e) {
                        this.saveStatus = '⚠️ Sambungan terputus';
                    } finally {
                        this.isSaving = false;
                    }
                }
            };
        }
    </script>
</body>
</html>

