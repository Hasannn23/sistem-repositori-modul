<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-xs text-gray-300">/</span>
                    <span class="text-xs text-gray-500 font-medium">Review Pengajuan Modul</span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Review & Persetujuan Modul Praktikum
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola dan periksa modul praktikum yang diajukan oleh dosen di lingkungan <span class="font-semibold text-gray-800">{{ auth()->user()->department->name ?? 'Jurusan Anda' }}</span>.
                </p>
            </div>

            <!-- Antrean Badge -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 text-blue-800 font-semibold text-sm rounded-xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>{{ $modules->count() }} Modul Menunggu ACC</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{
        selectedModuleTitle: '',
        rejectActionUrl: '',
        openRejectModal(title, actionUrl) {
            this.selectedModuleTitle = title;
            this.rejectActionUrl = actionUrl;
            $dispatch('open-modal', 'reject-module-modal');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs transition">
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
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex items-start gap-3 shadow-xs transition">
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

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 shadow-xs">
                    <p class="text-sm font-semibold mb-1">Terdapat kesalahan pada input Anda:</p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Konten Utama: Daftar Modul Waiting Review -->
            @if ($modules->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Semua Modul Telah Ditinjau</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">
                        Saat ini tidak ada draf modul praktikum yang sedang menunggu persetujuan (ACC) dari dosen di jurusan Anda.
                    </p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-base text-gray-900">
                                Antrean Pengajuan Modul ({{ $modules->count() }})
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Periksa konten modul PDF, lalu berikan persetujuan atau catatan revisi.
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                            ● Menunggu Review
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Judul Modul</th>
                                    <th class="py-3.5 px-6">Dosen Pengampu</th>
                                    <th class="py-3.5 px-6">Ruang Kelas</th>
                                    <th class="py-3.5 px-6">Tanggal Diajukan</th>
                                    <th class="py-3.5 px-6 text-right">Aksi & Keputusan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($modules as $module)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <!-- Judul Modul -->
                                        <td class="py-4 px-6">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-gray-900 block leading-tight">
                                                        {{ $module->title }}
                                                    </span>
                                                    <span class="text-xs text-gray-400 font-mono mt-0.5 block truncate max-w-xs">
                                                        {{ basename($module->file_path) }}
                                                    </span>
                                                    @if ($module->kajur_notes)
                                                        <div class="mt-1.5 text-xs text-amber-700 bg-amber-50 p-1.5 rounded border border-amber-200">
                                                            <strong class="font-semibold">Catatan Sebelumnya:</strong> {{ Str::limit($module->kajur_notes, 80) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Dosen Pengampu -->
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ $module->courseClass?->dosen?->initials() ?? 'DS' }}
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900 block leading-tight">
                                                        {{ $module->courseClass?->dosen?->name ?? 'Dosen Tidak Diketahui' }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 block">
                                                        {{ $module->courseClass?->dosen?->email ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Nama Kelas -->
                                        <td class="py-4 px-6">
                                            <div>
                                                <span class="font-medium text-gray-800 block">
                                                    {{ $module->courseClass?->name ?? '-' }}
                                                </span>
                                                @if ($module->courseClass?->referral_code)
                                                    <span class="inline-block font-mono text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 mt-0.5">
                                                        {{ $module->courseClass->referral_code }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Tanggal Pengajuan -->
                                        <td class="py-4 px-6 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $module->updated_at->format('d M Y, H:i') }}
                                        </td>

                                        <!-- Aksi Keputusan Kajur -->
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <!-- Tombol Lihat PDF -->
                                                <a href="{{ route('modules.show', $module->id) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 hover:border-gray-400 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                                                   title="Buka dan baca dokumen modul PDF">
                                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <span>Lihat PDF</span>
                                                </a>

                                                <!-- Tombol Setujui (ACC) -->
                                                <form method="POST" action="{{ route('kajur.modules.approve', $module->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui (ACC) modul ini? Modul akan terbuka untuk akses publik.');" class="inline">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold shadow-xs transition"
                                                        title="Setujui modul ini agar dapat diunduh publik">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span>Setujui (ACC)</span>
                                                    </button>
                                                </form>

                                                <!-- Tombol Kembalikan (Revisi) -->
                                                <button
                                                    type="button"
                                                    x-on:click="openRejectModal('{{ addslashes($module->title) }}', '{{ route('kajur.modules.reject', $module->id) }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-semibold shadow-xs transition"
                                                    title="Kembalikan modul ke dosen dengan catatan revisi">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                    </svg>
                                                    <span>Kembalikan (Revisi)</span>
                                                </button>
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

        <!-- Modal Alasan Revisi Kajur -->
        <x-modal name="reject-module-modal" focusable maxWidth="md">
            <form method="POST" :action="rejectActionUrl" class="p-6">
                @csrf

                <!-- Header Modal -->
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Kembalikan Modul (Revisi)
                        </h3>
                        <p class="text-xs text-gray-500">
                            Berikan catatan evaluasi agar dosen pengampu dapat memperbaiki modul praktikum.
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Modul yang ditolak:</span>
                        <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedModuleTitle"></p>
                    </div>

                    <div>
                        <label for="kajur_notes" class="block text-sm font-semibold text-gray-700 mb-1">
                            Catatan Revisi / Alasan Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="kajur_notes"
                            id="kajur_notes"
                            rows="4"
                            required
                            placeholder="Tuliskan bagian mana yang perlu diperbaiki atau dilengkapi oleh dosen pengampu..."
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-red-500 focus:ring-red-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400"></textarea>
                        @error('kajur_notes')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="mt-6 pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Kirim Catatan Revisi</span>
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
