<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Fakultas') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manajemen data master fakultas di lingkungan universitas.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('faculties.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Fakultas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Notification Alerts --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Sukses</h4>
                        <p class="text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Peringatan</h4>
                        <p class="text-sm mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Fakultas</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Total {{ $faculties->total() }} fakultas terdaftar</p>
                    </div>
                </div>

                @if ($faculties->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-4 border border-gray-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">Belum Ada Fakultas</h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Klik tombol "Tambah Fakultas" untuk mendaftarkan fakultas pertama.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    <th class="py-3.5 px-6 w-16">No</th>
                                    <th class="py-3.5 px-6">Nama Fakultas</th>
                                    <th class="py-3.5 px-6">Jumlah Jurusan</th>
                                    <th class="py-3.5 px-6">Tanggal Dibuat</th>
                                    <th class="py-3.5 px-6 text-right w-44">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($faculties as $index => $faculty)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-4 px-6 text-gray-500 font-mono text-xs">
                                            {{ $faculties->firstItem() + $index }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-gray-900 block">
                                                {{ $faculty->name }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $faculty->departments_count }} Jurusan
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 text-xs">
                                            {{ $faculty->created_at ? $faculty->created_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('faculties.edit', $faculty) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 hover:border-gray-400 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                                                    title="Edit Fakultas"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                <form
                                                    action="{{ route('faculties.destroy', $faculty) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus fakultas {{ $faculty->name }}?')"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 text-red-700 text-xs font-semibold transition"
                                                        title="Hapus Fakultas"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($faculties->hasPages())
                        <div class="p-6 border-t border-gray-100">
                            {{ $faculties->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
