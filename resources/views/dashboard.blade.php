<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (auth()->user()->role == 'super_admin')
                {{-- 1. Super Admin View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Super Admin
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Selamat Datang sebagai Super Admin
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Halo, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>. Anda memiliki akses penuh untuk mengelola seluruh struktur data dan manajemen pengguna dalam sistem repositori modul ini.
                        </p>
                    </div>
                </div>

                {{-- Action Menu Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Card 1: Kelola Fakultas & Jurusan --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Kelola Fakultas & Jurusan</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Kelola data master fakultas, program studi, serta jurusan yang terdaftar di lingkungan kampus.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Kelola Fakultas & Jurusan
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Kelola Pengguna --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Kelola Pengguna</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Manajemen akun pengguna, hak akses, dan penetapan role bagi Dosen, Kepala Jurusan, dan Mahasiswa.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Kelola Pengguna
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role == 'kajur')
                {{-- 2. Kajur View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Kepala Jurusan
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                Jurusan: {{ auth()->user()->department->name ?? 'Belum ditentukan' }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Selamat Datang sebagai Kepala Jurusan
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Halo, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span> (Jurusan: <span class="font-medium text-gray-900">{{ auth()->user()->department->name ?? 'Belum ditentukan' }}</span>). Anda dapat mereview pengajuan modul praktikum dari para dosen dan memantau modul yang telah disetujui.
                        </p>
                    </div>
                </div>

                {{-- Action Menu Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Card 1: Review Pengajuan Modul --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-amber-50 text-amber-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Review Pengajuan Modul</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Periksa, verifikasi kelayakan, dan berikan persetujuan untuk draf modul praktikum yang diajukan oleh dosen.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Review Pengajuan Modul
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Daftar Modul Disetujui --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Daftar Modul Disetujui</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Lihat daftar dan katalog arsip repositori modul praktikum yang telah disetujui di lingkungan jurusan Anda.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Daftar Modul Disetujui
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role == 'dosen')
                {{-- 3. Dosen View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                Dosen
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Selamat Datang sebagai Dosen
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Halo, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>. Anda dapat membuat ruang kelas praktikum baru dan mengunggah modul praktikum untuk diajukan ke Kepala Jurusan.
                        </p>
                    </div>
                </div>

                {{-- Action Menu Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Card 1: Buat Kelas Baru --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Buat Kelas Baru</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Buka kelas perkuliahan atau praktikum baru dan buat kode referral untuk dibagikan kepada mahasiswa.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Buat Kelas Baru
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Upload Modul Praktikum --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Upload Modul Praktikum</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Unggah dokumen modul praktikum (PDF) ke kelas yang Anda ampu untuk ditinjau oleh Kepala Jurusan.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Upload Modul Praktikum
                            </a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role == 'mahasiswa')
                {{-- 4. Mahasiswa View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                Mahasiswa
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Selamat Datang sebagai Mahasiswa
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Halo, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span>. Anda dapat bergabung ke dalam kelas praktikum dengan kode referral dan mengunduh modul perkuliahan Anda.
                        </p>
                    </div>
                </div>

                {{-- Action Menu Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Card 1: Gabung Kelas (Kode Referral) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Gabung Kelas (Kode Referral)</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Masukkan kode referral yang dibagikan oleh dosen untuk mendaftar dan bergabung ke kelas praktikum.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Gabung Kelas (Kode Referral)
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Modul Saya --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between border border-gray-100 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Modul Saya</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">
                                Akses dan unduh file modul praktikum yang telah dirilis pada seluruh kelas praktikum yang Anda ikuti.
                            </p>
                        </div>
                        <div>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Modul Saya
                            </a>
                        </div>
                    </div>
                </div>

            @else
                {{-- Fallback View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900">Selamat Datang, {{ auth()->user()->name }}!</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("You're logged in!") }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
