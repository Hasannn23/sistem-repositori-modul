<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-emerald-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Dasbor</span>
                    </a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        {{ $department->name ?? 'Kurikulum Prodi' }}
                    </span>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs text-slate-500 font-medium">Mata Kuliah</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    Daftar Mata Kuliah (Kurikulum)
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Kelola data master mata kuliah, bobot SKS, dan silabus untuk program studi <span class="font-semibold text-slate-800">{{ $department->name ?? 'Jurusan Anda' }}</span>.
                </p>
            </div>

            <!-- Header Action Button -->
            <div class="flex items-center gap-3">
                <a href="{{ route('kajur.courses.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Tambah Mata Kuliah</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen"
         x-data="{
             deleteModalOpen: false,
             deleteActionUrl: '',
             deleteCourseName: '',
             openDeleteModal(name, url) {
                 this.deleteCourseName = name;
                 this.deleteActionUrl = url;
                 this.deleteModalOpen = true;
             }
         }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-emerald-100 text-emerald-700 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
                    </div>
                    <button type="button" @click="$el.closest('div').remove()" class="text-emerald-600 hover:text-emerald-800 font-bold text-lg">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-rose-100 text-rose-700 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-rose-900">{{ session('error') }}</p>
                    </div>
                    <button type="button" @click="$el.closest('div').remove()" class="text-rose-600 hover:text-rose-800 font-bold text-lg">&times;</button>
                </div>
            @endif

            <!-- Summary Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-slate-500 block">Total Mata Kuliah</span>
                        <h4 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCourses }}</h4>
                        <span class="text-xs text-slate-500 mt-0.5 block">Terdaftar pada jurusan ini</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-slate-500 block">Akumulasi Bobot SKS</span>
                        <h4 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCredits }} <span class="text-sm font-medium text-slate-500">SKS</span></h4>
                        <span class="text-xs text-slate-500 mt-0.5 block">Total bobot kurikulum aktif</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0 border border-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-slate-500 block">Program Studi</span>
                        <h4 class="text-base font-bold text-slate-900 mt-1 truncate max-w-[200px]" title="{{ $department->name ?? '-' }}">
                            {{ $department->name ?? 'Belum ditentukan' }}
                        </h4>
                        <span class="text-xs text-emerald-700 font-medium mt-0.5 block">Fakultas {{ $department->faculty->name ?? '-' }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Search Bar & Controls -->
            <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form method="GET" action="{{ route('kajur.courses.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative w-full sm:w-80">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari kode MK, nama mata kuliah..."
                            class="w-full text-xs sm:text-sm rounded-xl border-slate-300 py-2.5 pl-9 pr-3 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <button type="submit"
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-semibold rounded-xl transition-colors shrink-0">
                        Cari
                    </button>

                    @if ($search)
                        <a href="{{ route('kajur.courses.index') }}"
                           class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors shrink-0"
                           title="Reset filter pencarian">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800">{{ $courses->total() }}</span> mata kuliah
                </div>
            </div>

            <!-- Table Card Container -->
            @if ($courses->isEmpty())
                <div class="bg-white border border-slate-200/80 rounded-xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mb-4 border border-emerald-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    @if ($search)
                        <h3 class="text-base font-bold text-slate-900 mb-1">Tidak Ditemukan Hasil Pencarian</h3>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto mb-5">
                            Tidak ada mata kuliah yang cocok dengan kata kunci &ldquo;<span class="font-semibold text-slate-800">{{ $search }}</span>&rdquo;.
                        </p>
                        <a href="{{ route('kajur.courses.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Kembali ke Semua Mata Kuliah
                        </a>
                    @else
                        <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Mata Kuliah</h3>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto mb-5">
                            Program studi Anda belum memiliki mata kuliah yang terdaftar. Tambahkan mata kuliah baru untuk memulai penyusunan kurikulum dan kelas perkuliahan.
                        </p>
                        <a href="{{ route('kajur.courses.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>+ Buat Mata Kuliah Pertama</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">
                                Kurikulum Program Studi ({{ $courses->total() }})
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Daftar seluruh mata kuliah aktif yang dapat dibuka kelasnya oleh para dosen.
                            </p>
                        </div>
                    </div>

                    {{-- Mobile View: Cards --}}
                    <div class="block lg:hidden divide-y divide-slate-100">
                        @foreach ($courses as $course)
                            <div class="p-4 hover:bg-slate-50/60 transition-colors space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                {{ $course->code }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                {{ $course->credit_hours }} SKS
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 text-sm leading-snug">
                                            {{ $course->name }}
                                        </h4>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <a href="{{ route('kajur.courses.edit', $course) }}"
                                           class="p-2 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg border border-slate-200 transition-colors"
                                           title="Edit Mata Kuliah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                @click="openDeleteModal('{{ addslashes($course->code . ' - ' . $course->name) }}', '{{ route('kajur.courses.destroy', $course) }}')"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 transition-colors"
                                                title="Hapus Mata Kuliah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @if ($course->description)
                                    <p class="text-xs text-slate-600 line-clamp-2">
                                        {{ $course->description }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-4 text-xs text-slate-500 pt-1 border-t border-slate-100">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"></path>
                                        </svg>
                                        <span>{{ $course->classes_count ?? 0 }} Kelas</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>{{ $course->modules_count ?? 0 }} Modul</span>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop View: Table --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Kode MK</th>
                                    <th class="py-3.5 px-6">Nama Mata Kuliah</th>
                                    <th class="py-3.5 px-6">Bobot SKS</th>
                                    <th class="py-3.5 px-6">Kelas Aktif</th>
                                    <th class="py-3.5 px-6">Modul</th>
                                    <th class="py-3.5 px-6">Deskripsi</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach ($courses as $course)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Kode MK -->
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                                {{ $course->code }}
                                            </span>
                                        </td>

                                        <!-- Nama MK -->
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-slate-900 block leading-tight">
                                                {{ $course->name }}
                                            </span>
                                            <span class="text-xs text-slate-500 mt-0.5 block">
                                                {{ $course->department->name ?? '-' }}
                                            </span>
                                        </td>

                                        <!-- Bobot SKS -->
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>{{ $course->credit_hours }} SKS</span>
                                            </span>
                                        </td>

                                        <!-- Kelas Terhubung -->
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                                <span class="w-2 h-2 rounded-full {{ ($course->classes_count ?? 0) > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                                <span>{{ $course->classes_count ?? 0 }} Kelas</span>
                                            </span>
                                        </td>

                                        <!-- Modul Terhubung -->
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                                <span class="w-2 h-2 rounded-full {{ ($course->modules_count ?? 0) > 0 ? 'bg-indigo-500' : 'bg-slate-300' }}"></span>
                                                <span>{{ $course->modules_count ?? 0 }} Modul</span>
                                            </span>
                                        </td>

                                        <!-- Deskripsi -->
                                        <td class="py-4 px-6 max-w-xs">
                                            @if ($course->description)
                                                <span class="text-xs text-slate-600 line-clamp-2" title="{{ $course->description }}">
                                                    {{ $course->description }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Tidak ada deskripsi</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <!-- Edit Button -->
                                                <a href="{{ route('kajur.courses.edit', $course) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:text-emerald-800 bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-lg transition-colors shadow-2xs"
                                                   title="Edit Mata Kuliah">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                <!-- Delete Button -->
                                                <button type="button"
                                                        @click="openDeleteModal('{{ addslashes($course->code . ' - ' . $course->name) }}', '{{ route('kajur.courses.destroy', $course) }}')"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 rounded-lg transition-colors shadow-2xs"
                                                        title="Hapus Mata Kuliah">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($courses->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Alpine Modal Konfirmasi Hapus Mata Kuliah -->
        <div x-show="deleteModalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 overflow-hidden"
                 @click.away="deleteModalOpen = false">
                
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Hapus Mata Kuliah?
                        </h3>
                        <p class="text-xs text-slate-500">
                            Tindakan ini akan memindahkan mata kuliah ke daftar terhapus.
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Mata Kuliah yang akan dihapus:</p>
                    <p class="text-sm font-bold text-slate-900 mt-1 p-3 bg-slate-50 rounded-xl border border-slate-200 font-mono" x-text="deleteCourseName"></p>
                    <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                        Pastikan mata kuliah ini tidak sedang memiliki kelas atau modul aktif yang dibutuhkan mahasiswa.
                    </p>
                </div>

                <!-- Form Aksi Hapus -->
                <form method="POST" :action="deleteActionUrl" class="mt-6 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            @click="deleteModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-semibold shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Ya, Hapus Mata Kuliah</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

