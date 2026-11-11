<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    {{ __('Sayembara Penyusunan Modul') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Kompetisi pengusulan modul praktikum program studi untuk menetapkan Master Modul resmi.
                </p>
            </div>

            @if (in_array(auth()->user()->role, ['kajur', 'super_admin']))
                <div>
                    <a href="{{ route('sayembaras.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buka Sayembara Baru</span>
                    </a>
                </div>
            @endif
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

            <!-- Filter & Pencarian -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <!-- Status Tabs -->
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl">
                    <a href="{{ route('sayembaras.index', array_filter(['search' => $search])) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ empty($status) ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                        Semua
                    </a>
                    <a href="{{ route('sayembaras.index', array_filter(['status' => 'open', 'search' => $search])) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $status === 'open' ? 'bg-white text-emerald-800 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                        Terbuka
                    </a>
                    <a href="{{ route('sayembaras.index', array_filter(['status' => 'closed', 'search' => $search])) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $status === 'closed' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                        Ditutup
                    </a>
                </div>

                <!-- Form Cari -->
                <form method="GET" action="{{ route('sayembaras.index') }}" class="flex items-center gap-2">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="relative w-full sm:w-72">
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari sayembara / matkul..."
                               class="w-full text-xs sm:text-sm pl-9 pr-3 py-2 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs sm:text-sm font-semibold rounded-xl transition">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('sayembaras.index', array_filter(['status' => $status])) }}" class="px-3 py-2 text-xs text-gray-500 hover:text-gray-800">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- List Sayembara Cards -->
            @if ($sayembaras->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Sayembara</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                        Saat ini belum ada sayembara pengusulan modul yang dibuka pada kategori atau pencarian ini.
                    </p>
                    @if (in_array(auth()->user()->role, ['kajur', 'super_admin']))
                        <a href="{{ route('sayembaras.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-xs transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buka Sayembara Pertama</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($sayembaras as $item)
                        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                        {{ $item->status === 'open' ? 'Terbuka' : 'Ditutup' }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $item->modules_count }} Modul Diajukan
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition">
                                    <a href="{{ route('sayembaras.show', $item) }}" class="hover:underline">
                                        {{ $item->title }}
                                    </a>
                                </h3>

                                <div class="mt-2.5 flex items-center gap-2 text-xs text-emerald-800 font-semibold bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span>{{ $item->course->name ?? 'Mata Kuliah' }} ({{ $item->course->code ?? '-' }}) • {{ $item->course->credit_hours ?? 0 }} SKS</span>
                                </div>

                                <p class="text-xs sm:text-sm text-gray-600 mt-3 line-clamp-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>

                                {{-- Live Participant / Social Proof --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center">
                                    <x-sayembara-participants :sayembara="$item" :variant="auth()->user()->role === 'kajur' ? 'kajur' : 'dosen'" />
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 block">Batas Waktu:</span>
                                    <span class="font-bold text-gray-800">{{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}</span>
                                </div>
                                <a href="{{ route('sayembaras.show', $item) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition shadow-xs">
                                    <span>Detail Sayembara</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-6">
                    {{ $sayembaras->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

