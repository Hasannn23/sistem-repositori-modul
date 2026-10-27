<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Daftar Kelas</span>
                    </a>
                    <span class="text-xs text-gray-300">/</span>
                    <span class="text-xs text-gray-500 font-medium truncate max-w-xs">
                        {{ $selectedClass ? $selectedClass->name : 'Semua Modul' }}
                    </span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ $selectedClass ? 'Modul: ' . $selectedClass->name : 'Daftar Seluruh Modul Praktikum' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Unggah dokumen modul praktikum PDF. Modul baru berstatus <span class="font-medium text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Draft</span> dan terkunci hingga disetujui Kepala Jurusan.
                </p>
            </div>

            <!-- Tombol Upload Modul (Buka Modal Breeze) -->
            <div>
                <button
                    type="button"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'upload-module')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-sm shadow-emerald-600/30 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Unggah Modul Praktikum</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm transition">
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
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex items-start gap-3 shadow-sm transition">
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

            <!-- Banner Info Kelas Terpilih (jika difilter berdasarkan class_id) -->
            @if ($selectedClass)
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruang Kelas Aktif</span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $modules->count() }} Modul
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mt-0.5">{{ $selectedClass->name }}</h3>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="text-right">
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kode Referral</span>
                            <span class="font-mono text-base font-extrabold text-emerald-800 tracking-wider">{{ $selectedClass->referral_code }}</span>
                        </div>
                        <a href="{{ route('classes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Pindah Kelas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Daftar Modul Praktikum -->
            @if ($modules->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Modul Praktikum</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                        Belum ada berkas modul PDF yang diunggah untuk {{ $selectedClass ? 'kelas ini' : 'seluruh kelas Anda' }}. Unggah modul praktikum pertama Anda sekarang.
                    </p>
                    <button
                        type="button"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'upload-module')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-xs transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Unggah Modul PDF Pertama</span>
                    </button>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-base text-gray-900">
                            Daftar Modul ({{ $modules->count() }})
                        </h3>
                        <span class="text-xs text-gray-500">
                            Format PDF • Maksimal 5MB
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Judul Modul</th>
                                    @if (!$selectedClass)
                                        <th class="py-3.5 px-6">Ruang Kelas</th>
                                    @endif
                                    <th class="py-3.5 px-6">Tipe & Status</th>
                                    <th class="py-3.5 px-6">Akses Publik</th>
                                    <th class="py-3.5 px-6">Tanggal Unggah</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($modules as $module)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <!-- Judul Modul & File Icon -->
                                        <td class="py-4 px-6">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <span class="font-bold text-gray-900 block leading-tight">
                                                        {{ $module->title }}
                                                    </span>
                                                    <span class="text-xs text-gray-400 font-mono mt-0.5 block truncate max-w-xs">
                                                        {{ basename($module->file_path) }}
                                                    </span>

                                                    <!-- Catatan Revisi Kajur -->
                                                    @if ($module->status == 'revision')
                                                        @if ($module->kajur_notes)
                                                            <div class="mt-2 p-2.5 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800 flex items-start gap-2 max-w-md">
                                                                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                </svg>
                                                                <div class="min-w-0">
                                                                    <strong class="font-semibold block text-red-900">Catatan Revisi Kajur:</strong>
                                                                    <p class="whitespace-pre-line mt-0.5 text-red-700">{{ $module->kajur_notes }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        @if (!$selectedClass)
                                            <td class="py-4 px-6 text-gray-700 font-medium">
                                                {{ $module->courseClass->name ?? '-' }}
                                            </td>
                                        @endif

                                        <!-- Tipe & Status -->
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col gap-1.5 items-start">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700">
                                                    {{ strtoupper($module->storage_type) }}
                                                </span>

                                                @if ($module->status === 'approved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                                        ● Disetujui
                                                    </span>
                                                @elseif ($module->status === 'waiting_review')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                        ● Menunggu Review
                                                    </span>
                                                @elseif ($module->status === 'revision')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                                        ● Perlu Revisi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        ● Draft
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Akses Publik (Terkunci / Terbuka) -->
                                        <td class="py-4 px-6">
                                            @if ($module->is_locked)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    <span>Terkunci</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>Terbuka</span>
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Tanggal -->
                                        <td class="py-4 px-6 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $module->created_at->format('d M Y, H:i') }}
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <!-- Tombol Ajukan ke Kajur (hanya status draft) -->
                                                @if ($module->status === 'draft')
                                                    <form method="POST" action="{{ route('modules.submit', $module->id) }}" class="inline">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold shadow-xs transition"
                                                            title="Ajukan ke Kajur">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Ajukan ke Kajur</span>
                                                        </button>
                                                    </form>
                                                @elseif ($module->status === 'revision')
                                                    <!-- Form Upload Perbaikan Revisi -->
                                                    <form method="POST" action="{{ route('modules.updateRevision', $module->id) }}" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                                                        @csrf
                                                        <input type="file" name="file" accept=".pdf" required class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer max-w-[200px]">
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-semibold shadow-xs transition"
                                                            title="Upload Perbaikan">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                            </svg>
                                                            <span>Upload Perbaikan</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Download / Preview PDF -->
                                                <a href="{{ route('modules.show', $module->id) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-emerald-300 text-xs font-semibold text-gray-700 hover:text-emerald-700 hover:bg-emerald-50/50 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <span>Lihat PDF</span>
                                                </a>

                                                <!-- Hapus Modul -->
                                                <form method="POST" action="{{ route('modules.destroy', $module->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul praktikum ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Hapus Modul">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <!-- Modal Upload Modul Praktikum (PDF) menggunakan Breeze x-modal -->
        <x-modal name="upload-module" :show="$errors->any()" focusable maxWidth="lg">
            <form method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data" x-data="{
                fileName: '',
                fileSize: '',
                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.fileName = file.name;
                        this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    } else {
                        this.fileName = '';
                        this.fileSize = '';
                    }
                }
            }">
                @csrf

                <!-- Header Modal -->
                <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                Unggah Modul Praktikum
                            </h3>
                            <p class="text-xs text-gray-500">
                                Unggah file berkas PDF modul perkuliahan (Maksimal 5MB).
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4">
                    <!-- Pilih Ruang Kelas -->
                    <div>
                        <label for="course_class_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Ruang Kelas <span class="text-red-500">*</span>
                        </label>
                        @if ($selectedClass)
                            <input type="hidden" name="course_class_id" value="{{ $selectedClass->id }}">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm font-semibold text-gray-800 flex items-center justify-between">
                                <span>{{ $selectedClass->name }}</span>
                                <span class="font-mono text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    {{ $selectedClass->referral_code }}
                                </span>
                            </div>
                        @else
                            <select
                                name="course_class_id"
                                id="course_class_id"
                                required
                                class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('course_class_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="" disabled selected>Pilih Ruang Kelas</option>
                                @foreach ($myClasses as $classItem)
                                    <option value="{{ $classItem->id }}" {{ old('course_class_id') == $classItem->id ? 'selected' : '' }}>
                                        {{ $classItem->name }} ({{ $classItem->referral_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_class_id')
                                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Judul Modul -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">
                            Judul Modul Praktikum <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            required
                            value="{{ old('title') }}"
                            placeholder="Contoh: Modul 1: Dasar HTML, CSS, dan Git"
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" />
                        @error('title')
                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Upload Berkas PDF (Max 5MB) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Berkas File Modul (PDF, Maks. 5MB) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-colors border-gray-300 hover:border-emerald-400 bg-gray-50/50 @error('file') border-red-300 @enderror">
                            <div class="space-y-2 text-center">
                                <div class="w-10 h-10 mx-auto text-emerald-600">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="file" class="relative cursor-pointer rounded-md font-semibold text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                        <span>Pilih file PDF</span>
                                        <input
                                            id="file"
                                            name="file"
                                            type="file"
                                            accept=".pdf"
                                            required
                                            @change="handleFileSelect"
                                            class="sr-only">
                                    </label>
                                    <p class="pl-1">atau seret ke sini</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Hanya dokumen berkas .PDF (maksimal 5 MB)
                                </p>
                                <!-- File info after selection -->
                                <template x-if="fileName">
                                    <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-800 rounded-lg text-xs font-semibold border border-emerald-200">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-text="fileName"></span>
                                        <span class="text-emerald-600" x-text="'(' + fileSize + ')'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('file')
                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Info Box Status & Review -->
                    <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200 flex items-start gap-3 text-amber-900">
                        <div class="p-1 bg-amber-100 text-amber-700 rounded-lg shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-xs space-y-0.5">
                            <p class="font-bold text-amber-900">Ketentuan Alur Modul</p>
                            <p class="text-amber-800 leading-relaxed">
                                Modul otomatis disimpan dengan status <span class="font-bold">Draft</span> dan <span class="font-bold">Terkunci</span> (tidak bisa diunduh publik) sampai diajukan dan disetujui oleh Kepala Jurusan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-white transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold shadow-xs shadow-emerald-600/30 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Unggah Dokumen PDF</span>
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
