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
                    <a href="{{ route('sayembaras.index') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-colors">
                        Sayembara
                    </a>
                    <span class="text-xs text-slate-300">/</span>
                    <a href="{{ route('sayembaras.show', $sayembara) }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-colors truncate max-w-[150px]">
                        {{ $sayembara->title }}
                    </a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        Inisiasi Modul
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    Ikuti Sayembara Modul
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Inisiasi draf modul pembelajaran untuk mata kuliah <span class="font-semibold text-slate-800">{{ $sayembara->course->name }}</span> ({{ $sayembara->course->code }}).
                </p>
            </div>

            <div>
                <a href="{{ route('sayembaras.show', $sayembara) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 bg-white transition-colors shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Detail Sayembara</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Info Sayembara -->
            <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100 font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                            {{ $sayembara->course->code }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">
                            Batas Waktu: {{ $sayembara->end_date ? $sayembara->end_date->format('d M Y') : 'Selesai' }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 truncate">
                        {{ $sayembara->title }}
                    </h3>
                    <p class="text-xs text-slate-600 mt-1 line-clamp-2">
                        {{ $sayembara->description }}
                    </p>
                </div>
            </div>

            <!-- Error Notification Alert -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="p-1 bg-rose-100 text-rose-700 rounded-lg shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-rose-900">Periksa kembali data Anda:</p>
                            <ul class="list-disc list-inside text-xs text-rose-700 mt-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Inisiasi Sayembara Card -->
            <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-4">
                    <h3 class="text-sm font-bold text-slate-900">Formulir Inisiasi Draf Modul</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Langkah 1: Tentukan judul modul. Anda akan langsung diarahkan ke Ruang Kerja LMS Builder untuk menyusun materi pertemuan (1 s.d. 14+).
                    </p>
                </div>

                <form method="POST" action="{{ route('sayembaras.participate', $sayembara) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Judul Modul (Wajib) -->
                    <div>
                        <label for="title" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Judul Modul <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', 'Modul Pembelajaran ' . $sayembara->course->name) }}"
                            required
                            autofocus
                            placeholder="Contoh: Modul Praktikum Komprehensif {{ $sayembara->course->name }}"
                            class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400 @error('title') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        />
                        <p class="text-xs text-slate-500 mt-1">
                            Judul dapat diubah kapan saja di dalam antarmuka LMS Builder.
                        </p>
                        @error('title')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi Singkat (Opsional) -->
                    <div>
                        <label for="description" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Deskripsi Singkat (Opsional)
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="3"
                            placeholder="Ringkasan singkat cakupan modul, silabus, atau sasaran kompetensi yang akan dicapai..."
                            class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400 @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        >{{ old('description') }}</textarea>
                        <p class="text-xs text-slate-500 mt-1">
                            Maksimal 2000 karakter. Memberikan gambaran singkat kurikulum draf modul kepada Kepala Jurusan.
                        </p>
                        @error('description')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opsi Pautkan ke Kelas Tertentu (Opsional) -->
                    @if (isset($myClasses) && $myClasses->isNotEmpty())
                        <div>
                            <label for="course_class_id" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                                Pautkan ke Ruang Kelas Anda (Opsional)
                            </label>
                            <select
                                name="course_class_id"
                                id="course_class_id"
                                class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                            >
                                <option value="">-- Tidak Dipautkan ke Kelas Tertentu --</option>
                                @foreach ($myClasses as $class)
                                    <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->referral_code }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">
                                Jika modul ini juga akan Anda gunakan langsung pada kelas Anda semester ini.
                            </p>
                        </div>
                    @endif

                    <!-- Form Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('sayembaras.show', $sayembara) }}"
                           class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Simpan & Mulai Susun LMS</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

