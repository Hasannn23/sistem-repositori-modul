<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Repositori Modul Praktikum - Platform digital resmi untuk mengakses dan mengunduh modul praktikum perkuliahan.">

    <title>Katalog Kelas Praktikum - Sistem Repositori Modul</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 bg-[#F9FBFA] min-h-screen flex flex-col selection:bg-green-100 selection:text-green-800">

    <!-- Top Announcement Bar / Minimal Decor -->
    <div class="h-1 bg-gradient-to-r from-green-500 via-emerald-600 to-green-700 w-full"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                <!-- Brand / Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-xl">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-green-700 to-emerald-500 flex items-center justify-center text-white shadow-sm shadow-green-600/30 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-gray-900 text-base sm:text-lg leading-tight tracking-tight group-hover:text-green-700 transition-colors">
                            Repositori Modul <span class="text-green-600">Praktikum</span>
                        </span>
                        <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">
                            Sistem Repositori Akademik
                        </span>
                    </div>
                </a>

                <!-- Right Actions: Auth Links -->
                <nav class="flex items-center gap-2 sm:gap-3">
                    @if (Route::has('login'))
                        @auth
                            @php
                                $dashboardUrl = match(Auth::user()->role ?? '') {
                                    'mahasiswa' => route('student.dashboard'),
                                    'dosen' => route('classes.index'),
                                    'kajur' => route('kajur.modules.index'),
                                    default => route('dashboard'),
                                };
                            @endphp
                            <a href="{{ $dashboardUrl }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 active:bg-green-800 rounded-xl shadow-xs shadow-green-600/25 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Ke Dasbor</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 active:bg-green-800 rounded-xl shadow-xs shadow-green-600/25 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Masuk</span>
                            </a>
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container with Alpine for Search -->
    <main class="flex-grow" x-data="{ search: '{{ addslashes($search ?? '') }}' }">
        
        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-12 pb-16 lg:pt-20 lg:pb-24 border-b border-gray-100/80 bg-gradient-to-b from-white via-green-50/30 to-[#F9FBFA]">
            <!-- Subtle Ambient Background Glows -->
            <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-green-200/40 rounded-full blur-3xl -z-10"></div>
            <div class="pointer-events-none absolute top-1/2 right-10 w-72 h-72 bg-emerald-200/20 rounded-full blur-3xl -z-10"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Pill Status Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-green-50 text-green-800 ring-1 ring-green-200/70 mb-6 shadow-xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                    </span>
                    <span>Portal Resmi Repositori Modul Pembelajaran</span>
                </div>

                <!-- Big Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-[1.15] mb-6">
                    Katalog Ruang Kelas Praktikum <br class="hidden sm:inline" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-700 via-emerald-600 to-green-600">
                        Mudah, Cepat, dan Terverifikasi
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="max-w-2xl mx-auto text-base sm:text-lg text-gray-600 leading-relaxed mb-10">
                    Pilih ruang kelas praktikum di bawah ini untuk melihat daftar modul pembelajaran yang telah divalidasi dan disetujui.
                </p>

                <!-- Search Bar Container -->
                <div class="max-w-2xl mx-auto relative">
                    <form method="GET" action="{{ url('/') }}" class="relative flex items-center shadow-md shadow-green-900/5 rounded-2xl bg-white border border-gray-200/80 ring-1 ring-green-100 focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500 transition-all duration-200">
                        <div class="pl-4 pr-2 text-gray-400 pointer-events-none">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="search"
                            x-model="search"
                            placeholder="Cari nama kelas praktikum, atau dosen... (tekan Enter)"
                            class="w-full py-4 pr-12 text-sm sm:text-base text-gray-800 placeholder-gray-400 bg-transparent border-0 focus:outline-none focus:ring-0"
                        />
                        <button 
                            type="button"
                            x-show="search.length > 0" 
                            x-cloak 
                            @click="search = ''; window.location.href = '{{ url('/') }}'" 
                            class="absolute right-4 text-gray-400 hover:text-gray-600 p-1 rounded-full focus:outline-none"
                            title="Bersihkan pencarian"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Feature Badges -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-xs sm:text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs">✓</div>
                        <span>Materi Terverifikasi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs">✓</div>
                        <span>Langsung dari Dosen Pengampu</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Classes Catalog Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                        <span>Daftar Kelas Praktikum</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ $classes->total() }} Kelas
                        </span>
                    </h2>
                </div>

                <div x-show="search" x-cloak class="text-xs text-gray-500 bg-green-50 text-green-800 px-3 py-1.5 rounded-lg border border-green-200">
                    Menyaring hasil berdasarkan: <span class="font-semibold text-green-900" x-text="search"></span>
                </div>
            </div>

            <!-- Empty State -->
            @if ($classes->isEmpty())
                <div class="bg-white rounded-3xl border border-gray-200/80 p-12 text-center max-w-xl mx-auto shadow-xs my-8">
                    <div class="w-20 h-20 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mx-auto mb-5 ring-8 ring-green-50/50">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada kelas praktikum dengan modul yang disetujui.</h3>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                        Saat ini belum ada ruang kelas yang memiliki modul berstatus disetujui. Silakan periksa kembali di lain waktu.
                    </p>
                </div>
            @else
                <!-- Grid Daftar Kelas Praktikum -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($classes as $class)
                        @php
                            $dosenName = $class->dosen?->name ?? 'Dosen Belum Ditentukan';
                            $modulesCount = $class->modules_count ?? 0;
                        @endphp

                        <div 
                            x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                            class="group relative bg-white rounded-2xl border border-gray-200/90 hover:border-green-300 p-6 shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                        >
                            <!-- Card Upper Content -->
                            <div>
                                <!-- Top Bar: Modules Count Badge -->
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200/60">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Kelas Praktikum
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-green-200/60">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $modulesCount }} Modul
                                    </span>
                                </div>

                                <!-- Class Name -->
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-green-700 transition-colors duration-150 line-clamp-2 mb-4 leading-snug">
                                    {{ $class->name }}
                                </h3>
                            </div>

                            <!-- Card Lower Content -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col gap-4">
                                <!-- Dosen Pengampu Info -->
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-green-100 text-green-800 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-green-200/50">
                                        @php
                                            $nameParts = explode(' ', trim($dosenName));
                                            $initials = '';
                                            foreach (array_slice($nameParts, 0, 2) as $part) {
                                                $initials .= strtoupper(substr($part, 0, 1));
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

                                <!-- Action Button: Lihat Daftar Modul -->
                                <a 
                                    href="{{ route('katalog.class', $class) }}" 
                                    class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-green-700 bg-green-50 hover:bg-green-600 hover:text-white border border-green-200 hover:border-green-600 rounded-xl transition-all duration-200 shadow-2xs group-hover:shadow-xs group-hover:shadow-green-600/20 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                >
                                    <span>Lihat Daftar Modul</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $classes->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Clean Minimalist Footer -->
    <footer class="mt-auto bg-white border-t border-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-green-600 flex items-center justify-center text-white text-xs font-bold">
                        M
                    </div>
                    <span class="text-sm font-semibold text-gray-800">
                        Sistem Repositori Modul Praktikum
                    </span>
                </div>
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Sistem Informasi Repositori Modul Praktikum. Seluruh hak cipta dilindungi.
                </p>
                <div class="flex items-center gap-4 text-xs font-medium text-gray-500">
                    <a href="{{ url('/') }}" class="hover:text-green-700 transition-colors">Beranda</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-green-700 transition-colors">Dasbor</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-green-700 transition-colors">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
