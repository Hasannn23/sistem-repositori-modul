<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                    title="Kembali ke Dasbor"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $courseClass->name }}
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Ruang Kelas Praktikum &bull; Kode: <span class="font-mono font-semibold text-emerald-700">{{ $courseClass->referral_code }}</span>
                    </p>
                </div>
            </div>
            <div>
                <a
                    href="{{ route('student.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Kelas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Class Information Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0 border border-emerald-200/60 shadow-xs">
                            @php
                                $dosenName = $courseClass->dosen?->name ?? 'Dosen Belum Ditentukan';
                                $nameParts = explode(' ', trim($dosenName));
                                $initials = '';
                                foreach (array_slice($nameParts, 0, 2) as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                            @endphp
                            {{ $initials ?: 'DS' }}
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 mb-1">
                                Kode: {{ $courseClass->referral_code }}
                            </span>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $courseClass->name }}</h3>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-medium text-gray-800">Dosen Pengampu:</span> {{ $dosenName }}
                                </span>
                                @if ($courseClass->dosen?->department)
                                    <span class="text-gray-300">&bull;</span>
                                    <span>Jurusan: {{ $courseClass->dosen->department->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                        <div class="text-left md:text-right">
                            <span class="text-xs uppercase tracking-wider font-semibold text-gray-400">Total Modul Tersedia</span>
                            <p class="text-2xl font-extrabold text-emerald-600">{{ $modules->total() }} Modul</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approved Modules Section --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Daftar Modul Praktikum</h3>
                        <p class="text-sm text-gray-500">Materi dan modul yang telah disetujui (Approved) dan siap diakses.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $modules->total() }} Modul Approved
                    </span>
                </div>

                @if ($modules->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center max-w-lg mx-auto shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4 border border-amber-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">Belum Ada Modul yang Disetujui</h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">
                            Saat ini belum ada modul praktikum yang telah berstatus disetujui (approved) pada kelas ini. Silakan periksa kembali berkala.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($modules as $module)
                            @php
                                $fileUrl = Str::startsWith($module->file_path, ['http://', 'https://'])
                                    ? $module->file_path
                                    : (Storage::disk('public')->exists($module->file_path) ? asset('storage/' . $module->file_path) : route('modules.show', $module));
                                
                                $isPdf = strtolower($module->storage_type) === 'pdf' || Str::endsWith(strtolower($module->file_path), '.pdf');
                                $isGdrive = strtolower($module->storage_type) === 'gdrive' || Str::contains($module->file_path, 'drive.google.com') || str_starts_with($module->file_path, 'http');
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-200/80 hover:border-emerald-300 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                                <div>
                                    <!-- Top Badges: Status & Type -->
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Approved
                                        </span>

                                        @if ($isPdf)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M7 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-6-6H7zm7 1.5L17.5 7H14V3.5zM8 12h8v1.5H8V12zm0 3h8v1.5H8V15zm0 3h5v1.5H8V18z"/>
                                                </svg>
                                                PDF
                                            </span>
                                        @elseif ($isGdrive)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                Link
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                Dokumen
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Module Title -->
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition line-clamp-2 mb-2 leading-snug">
                                        {{ $module->title }}
                                    </h4>

                                    <!-- Date published -->
                                    <p class="text-xs text-gray-400">
                                        Diperbarui: {{ $module->updated_at ? $module->updated_at->isoFormat('D MMMM Y') : '-' }}
                                    </p>
                                </div>

                                <!-- Action Buttons: Download / Lihat -->
                                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center gap-2">
                                    <a
                                        href="{{ $fileUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center flex-1 gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-600 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>{{ $isGdrive ? 'Buka Link Modul' : 'Lihat Modul' }}</span>
                                    </a>

                                    @if ($isPdf)
                                        <a
                                            href="{{ $fileUrl }}"
                                            download
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center p-2.5 text-gray-600 hover:text-emerald-700 bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            title="Unduh File Modul"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $modules->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
