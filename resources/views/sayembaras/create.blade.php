<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sayembaras.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    {{ __('Buka Sayembara Modul Baru') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    Publikasikan sayembara penyusunan modul prodi untuk diikuti oleh para Dosen pengampu.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                <form method="POST" action="{{ route('sayembaras.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Pilih Mata Kuliah -->
                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Mata Kuliah (Program Studi) <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" id="course_id" required
                                class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('course_id') border-red-300 @enderror">
                            <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->code }} - {{ $course->name }} ({{ $course->credit_hours }} SKS) • {{ $course->department->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Judul Sayembara -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Judul Sayembara <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title') }}"
                               placeholder="Contoh: Sayembara Penyusunan Modul Praktikum Algoritma & Pemrograman 2026"
                               class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('title') border-red-300 @enderror" />
                        @error('title')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi & Ketentuan Sayembara -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Deskripsi, Panduan, & Ketentuan Sayembara <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="5" required
                                  placeholder="Jelaskan ruang lingkup modul, format materi, target capaian pembelajaran (CPL), serta kriteria penilaian modul..."
                                  class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periode Waktu: Tanggal Mulai & Batas Waktu -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Tanggal Mulai Pengusulan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" required
                                   value="{{ old('start_date', now()->toDateString()) }}"
                                   class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('start_date') border-red-300 @enderror" />
                            @error('start_date')
                                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Batas Waktu (Deadline) <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date" required
                                   value="{{ old('end_date', now()->addWeeks(2)->toDateString()) }}"
                                   class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('end_date') border-red-300 @enderror" />
                            @error('end_date')
                                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Upload Brosur / TOR (Opsional) -->
                    <div>
                        <label for="brochure" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Brosur / Dokumen Kerangka Acuan Kerja (Opsional, PDF/Gambar maks. 5MB)
                        </label>
                        <input type="file" name="brochure" id="brochure" accept=".pdf,image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-gray-200 rounded-xl" />
                        @error('brochure')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('sayembaras.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            Publikasikan Sayembara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

