<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('sayembaras.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sayembara->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            {{ $sayembara->status === 'open' ? 'Sayembara Terbuka' : 'Sayembara Ditutup' }}
                        </span>
                        <span class="text-xs font-medium text-gray-500">
                            Jurusan: {{ $sayembara->department->name ?? '-' }}
                        </span>
                    </div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight mt-1">
                        {{ $sayembara->title }}
                    </h2>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                @if ($sayembara->brochure_path)
                    <a href="{{ asset('storage/' . $sayembara->brochure_path) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs sm:text-sm rounded-xl shadow-2xs transition">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Unduh Brosur/TOR</span>
                    </a>
                @endif

                @if (in_array(auth()->user()->role, ['kajur', 'super_admin']) && $sayembara->status === 'open')
                    <form method="POST" action="{{ route('sayembaras.close', $sayembara) }}" onsubmit="return confirm('Tutup sayembara ini? Dosen tidak dapat lagi mengajukan modul.');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-700 font-semibold text-xs sm:text-sm rounded-xl transition border border-gray-200">
                            Tutup Sayembara
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Ringkasan Mata Kuliah & Periode -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Mata Kuliah</span>
                        <h4 class="font-bold text-gray-900 text-base">{{ $sayembara->course->name ?? '-' }}</h4>
                        <span class="text-xs text-emerald-700 font-semibold">{{ $sayembara->course->code ?? '-' }} • {{ $sayembara->course->credit_hours ?? 0 }} SKS</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Periode Pengusulan</span>
                        <h4 class="font-bold text-gray-900 text-sm">
                            {{ $sayembara->start_date ? $sayembara->start_date->format('d M Y') : '-' }} s/d {{ $sayembara->end_date ? $sayembara->end_date->format('d M Y') : '-' }}
                        </h4>
                        <span class="text-xs {{ $sayembara->status === 'open' ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                            {{ $sayembara->status === 'open' ? 'Sedang Berlangsung' : 'Selesai' }}
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Partisipasi Dosen</span>
                        <div class="mt-1">
                            <x-sayembara-participants :sayembara="$sayembara" :variant="auth()->user()->role === 'kajur' ? 'kajur' : 'dosen'" />
                        </div>
                        <span class="text-[11px] text-gray-500 block mt-1">
                            Total: <strong>{{ $sayembara->modules->count() }} modul</strong> diajukan
                        </span>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Sayembara -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
                <h3 class="font-bold text-base text-gray-900 mb-2">Panduan & Ketentuan Modul</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                    {{ $sayembara->description }}
                </p>
            </div>

            <!-- Form Ajukan Draf Modul untuk Dosen (LMS Builder Flow) -->
            @if (in_array(auth()->user()->role, ['dosen', 'super_admin']) && $sayembara->status === 'open')
                <div x-data="{ openForm: false }" class="bg-white rounded-2xl border border-emerald-200 shadow-xs overflow-hidden">
                    <div class="p-6 bg-emerald-50/50 border-b border-emerald-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg text-emerald-950">Ikuti Sayembara: Inisiasi Modul LMS</h3>
                            <p class="text-xs text-emerald-800 mt-0.5">
                                Tentukan judul modul pembelajaran Anda, lalu mulai susun materi pertemuan di Ruang Kerja LMS Builder.
                            </p>
                        </div>
                        <button type="button" @click="openForm = ! openForm"
                                class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors duration-200">
                            <span x-text="openForm ? 'Tutup Formulir' : '+ Ikuti Sayembara'"></span>
                        </button>
                    </div>

                    <div x-show="openForm" x-transition class="p-6 space-y-4">
                        <form method="POST" action="{{ route('sayembaras.participate', $sayembara) }}" class="space-y-4">
                            @csrf

                            <!-- Judul Modul -->
                            <div>
                                <label for="title" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                                    Judul Modul <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" required
                                       placeholder="Contoh: Modul Praktikum Komprehensif {{ $sayembara->course->name }}"
                                       class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400">
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div>
                                <label for="description" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                                    Deskripsi Singkat (Opsional)
                                </label>
                                <textarea name="description" id="description" rows="3"
                                          placeholder="Ringkasan singkat cakupan modul atau pendekatan pembelajaran yang akan disusun..."
                                          class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 placeholder-slate-400"></textarea>
                            </div>

                            <!-- Opsi Kelas Terkait (Jika ada) -->
                            @if ($myClasses->isNotEmpty())
                                <div>
                                    <label for="course_class_id" class="block text-xs font-semibold tracking-wider uppercase text-slate-700 mb-1.5">
                                        Pautkan ke Ruang Kelas Anda (Opsional)
                                    </label>
                                    <select name="course_class_id" id="course_class_id"
                                            class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800">
                                        <option value="">-- Tidak Dipautkan ke Kelas Tertentu --</option>
                                        @foreach ($myClasses as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->referral_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-sm transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span>Simpan & Mulai Susun LMS</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Daftar Draf Modul yang Diajukan -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">Daftar Modul yang Diajukan ({{ $sayembara->modules->count() }})</h3>
                        <p class="text-xs text-gray-500">Usulan draf modul dari para dosen untuk dievaluasi oleh Kepala Jurusan.</p>
                    </div>
                </div>

                @if ($sayembara->modules->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-500">
                        Belum ada modul yang diajukan untuk sayembara ini.
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($sayembara->modules as $mod)
                            <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $mod->is_master ? 'bg-amber-50/40 border-l-4 border-amber-500' : '' }}">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        @if ($mod->is_master)
                                            <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-extrabold bg-amber-500 text-white shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                MASTER MODUL RESMI
                                            </span>
                                        @elseif ($mod->status === 'revision')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                Revisi
                                            </span>
                                        @elseif ($mod->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Disetujui
                                            </span>
                                        @elseif ($mod->status === 'waiting_review')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu Review
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst($mod->status) }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-500 font-mono uppercase bg-gray-100 px-2 py-0.5 rounded">
                                            {{ $mod->storage_type }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-gray-900 text-base">
                                        {{ $mod->title }}
                                    </h4>

                                    @if ($mod->status === 'revision' && ($mod->revision_note || $mod->kajur_notes))
                                        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 text-rose-700 rounded-r-xl my-2 text-sm shadow-2xs">
                                            <div class="flex items-center gap-1.5 font-bold mb-1 text-rose-800">
                                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span>Catatan Revisi dari Kajur:</span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-rose-800 leading-relaxed pl-5 whitespace-pre-line">
                                                {{ $mod->revision_note ?? $mod->kajur_notes }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-600">
                                        <span>Pengusul: <strong class="text-gray-800">{{ $mod->authorUser?->name ?? '-' }}</strong> {{ $mod->authorUser?->nidn ? '(NIDN: ' . $mod->authorUser->nidn . ')' : '' }}</span>
                                        <span>Diunggah: {{ $mod->created_at->format('d M Y, H:i') }}</span>
                                        @if ($mod->meetings->count() > 0)
                                            <span class="text-emerald-700 font-semibold">• {{ $mod->meetings->count() }} Pertemuan LMS</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <!-- LMS Reader -->
                                    <a href="{{ route('modules.lms', $mod) }}"
                                       class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-xs rounded-xl border border-emerald-200 transition">
                                        Buka LMS
                                    </a>

                                    <!-- LMS Builder (for Author or Super Admin, only when draft/revision and sayembara open) -->
                                    @if (auth()->id() === $mod->author_id || auth()->user()->role === 'super_admin' || ($mod->courseClass && $mod->courseClass->dosen_id === auth()->id()))
                                        @if (in_array($mod->status, ['draft', 'revision']) && !$mod->is_master && $sayembara->status === 'open')
                                            <a href="{{ route('dosen.modules.builder', $mod) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>{{ $mod->status === 'revision' ? 'Lanjut Edit di LMS Builder' : 'LMS Builder' }}</span>
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Review Modul (untuk Kajur / Super Admin) -->
                                    @if (in_array(auth()->user()->role, ['kajur', 'super_admin']))
                                        <a href="{{ route('kajur.modules.review', $mod->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-emerald-600 text-emerald-700 hover:bg-emerald-50 active:bg-emerald-100 font-semibold text-xs rounded-xl transition shadow-2xs">
                                            <span>🔍 Review Modul</span>
                                        </a>
                                    @endif

                                    <!-- Tombol Lihat Berkas / Link Asli -->
                                    @if ($mod->storage_type === 'link')
                                        <a href="{{ $mod->file_path }}" target="_blank"
                                           class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition">
                                            Link Asli
                                        </a>
                                    @elseif ($mod->file_path)
                                        <a href="{{ asset('storage/' . $mod->file_path) }}" target="_blank"
                                           class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition">
                                            PDF
                                        </a>
                                    @endif

                                    <!-- Tombol ACC Master Modul untuk Kajur -->
                                    @if (in_array(auth()->user()->role, ['kajur', 'super_admin']))
                                        @if ($mod->is_master)
                                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 flex items-center gap-1">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Telah Menjadi Master
                                            </span>
                                        @elseif ($sayembara->status === 'open')
                                            <form method="POST" action="{{ route('sayembaras.approve-master', [$sayembara, $mod]) }}"
                                                  onsubmit="return confirm('Tetapkan modul ini sebagai MASTER MODUL? Modul ini akan otomatis terpasang di semua kelas mata kuliah ini.');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>ACC Sebagai Master Modul</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                Sayembara Ditutup
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

