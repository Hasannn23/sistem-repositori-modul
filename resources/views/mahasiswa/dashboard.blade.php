<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dasbor Mahasiswa') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gabung ke ruang kelas praktikum dan akses modul pembelajaran Anda.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1.5 animate-pulse"></span>
                    Role: Mahasiswa
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Notification Alerts --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 flex items-start gap-3 shadow-sm">
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
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Peringatan</h4>
                        <p class="text-sm mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Terdapat Kesalahan</h4>
                        <ul class="list-disc list-inside text-sm mt-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- 1. Form Gabung Kelas Baru (Kode Referral) --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-xs">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Gabung Kelas Baru</h3>
                            <p class="text-sm text-gray-500">Masukkan kode referral yang diberikan oleh Dosen pengampu Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('student.join') }}" method="POST" class="mt-6">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1 relative">
                                <label for="referral_code" class="sr-only">Kode Referral</label>
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="referral_code"
                                    id="referral_code"
                                    value="{{ old('referral_code') }}"
                                    placeholder="Masukkan Kode Referral (cth: AB12CD)"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl uppercase tracking-wider font-mono text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                >
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shrink-0 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Gabung Kelas</span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">
                            * Kode referral biasanya terdiri dari 6 karakter acak dan bersifat unik untuk setiap kelas praktikum.
                        </p>
                    </form>
                </div>
            </div>

            {{-- 2. Daftar Kelas yang Diikuti --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-900">Kelas yang Diikuti</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $joinedClasses->count() }} Kelas
                        </span>
                    </div>
                </div>

                @if ($joinedClasses->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center max-w-lg mx-auto shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-4 border border-gray-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">Belum Ada Kelas yang Diikuti</h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">
                            Anda belum terdaftar di kelas praktikum manapun. Silakan minta kode referral kepada dosen pengampu Anda dan masukkan pada form di atas.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($joinedClasses as $class)
                            @php
                                $dosenName = $class->dosen?->name ?? 'Dosen Belum Ditentukan';
                                $modulesCount = $class->modules_count ?? 0;
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-200/80 hover:border-emerald-300 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                                <div>
                                    <!-- Header Badge: Referral Code -->
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            {{ $class->referral_code }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $modulesCount }} Modul
                                        </span>
                                    </div>

                                    <!-- Class Name -->
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition line-clamp-2 mb-4 leading-snug">
                                        {{ $class->name }}
                                    </h4>

                                    <!-- Lecturer Info -->
                                    <div class="flex items-center gap-3 py-3 border-t border-gray-100">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-emerald-200/50">
                                            @php
                                                $parts = explode(' ', trim($dosenName));
                                                $initials = '';
                                                foreach (array_slice($parts, 0, 2) as $p) {
                                                    $initials .= strtoupper(substr($p, 0, 1));
                                                }
                                            @endphp
                                            {{ $initials ?: 'DS' }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Dosen Pengampu</p>
                                            <p class="text-sm font-semibold text-gray-800 truncate" title="{{ $dosenName }}">
                                                {{ $dosenName }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button: Masuk Kelas -->
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a
                                        href="{{ route('student.class.show', $class) }}"
                                        class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-600 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        <span>Masuk Kelas</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
