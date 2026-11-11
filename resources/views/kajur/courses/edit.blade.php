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
                    <a href="{{ route('kajur.courses.index') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-colors">
                        Mata Kuliah
                    </a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-mono">
                        {{ $course->code }}
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    Edit Mata Kuliah: {{ $course->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Perbarui rincian kode MK, nama mata kuliah, bobot SKS, atau deskripsi silabus untuk prodi <span class="font-semibold text-slate-800">{{ $department->name ?? 'Jurusan Anda' }}</span>.
                </p>
            </div>

            <div>
                <a href="{{ route('kajur.courses.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 bg-white transition-colors shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
                            <p class="text-sm font-bold text-rose-900">Periksa kembali data yang Anda masukkan:</p>
                            <ul class="list-disc list-inside text-xs text-rose-700 mt-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                <!-- Info Header Notice -->
                <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-semibold tracking-wider uppercase text-slate-500 block">Afiliasi Program Studi</span>
                            <span class="text-sm font-extrabold text-slate-900">{{ $department->name ?? 'Jurusan Anda' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                            {{ $course->classes()->count() }} Kelas Aktif
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $course->modules()->count() }} Modul
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('kajur.courses.update', $course) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Kode Mata Kuliah -->
                    <div>
                        <label for="code" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Kode Mata Kuliah <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            value="{{ old('code', $course->code) }}"
                            required
                            placeholder="Contoh: INF-201 atau TIF-304"
                            style="text-transform: uppercase;"
                            class="w-full text-sm font-mono rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400 @error('code') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        />
                        <p class="text-xs text-slate-500 mt-1">
                            Kode unik mata kuliah (contoh: INF-201, SI-102). Kode ini akan dikonversi ke huruf besar otomatis.
                        </p>
                        @error('code')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Mata Kuliah -->
                    <div>
                        <label for="name" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Nama Mata Kuliah <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $course->name) }}"
                            required
                            placeholder="Contoh: Algoritma dan Struktur Data"
                            class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400 @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        />
                        <p class="text-xs text-slate-500 mt-1">
                            Nama lengkap mata kuliah sesuai kurikulum resmi prodi.
                        </p>
                        @error('name')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bobot SKS -->
                    <div>
                        <label for="credit_hours" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Bobot SKS (Sistem Kredit Semester) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="credit_hours"
                                id="credit_hours"
                                required
                                class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800 @error('credit_hours') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            >
                                <option value="" disabled>-- Pilih Bobot SKS --</option>
                                @foreach ([1, 2, 3, 4, 5, 6, 8] as $sks)
                                    <option value="{{ $sks }}" {{ old('credit_hours', $course->credit_hours) == $sks ? 'selected' : '' }}>
                                        {{ $sks }} SKS
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Bobot SKS untuk mata kuliah teori maupun praktikum di jurusan Anda.
                        </p>
                        @error('credit_hours')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi / Silabus Ringkas -->
                    <div>
                        <label for="description" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                            Deskripsi / Silabus Ringkas (Opsional)
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            placeholder="Tuliskan gambaran pokok bahasan mata kuliah, capaian pembelajaran (CPL), atau rincian materi yang diajarkan..."
                            class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400 @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        >{{ old('description', $course->description) }}</textarea>
                        <p class="text-xs text-slate-500 mt-1">
                            Maksimal 2000 karakter. Informasi ini dapat dilihat oleh dosen pengampu dan mahasiswa.
                        </p>
                        @error('description')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('kajur.courses.index') }}"
                           class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

