<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PRISMA - Pusat Repositori dan Integrasi Sayembara Modul Akademik. Standar baru pembelajaran praktikum informatika.">

    <title>PRISMA - Pusat Repositori dan Integrasi Sayembara Modul Akademik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-900 selection:bg-emerald-500 selection:text-white"
      x-data="{ scrolled: false }"
      @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Navigation Header (Transparent to Glassmorphic on Scroll) -->
    <header :class="scrolled 
                ? 'bg-slate-900/85 backdrop-blur-md border-b border-white/10 shadow-lg py-3 sm:py-3.5' 
                : 'bg-transparent border-b border-transparent py-5 sm:py-6'"
            class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <!-- Brand / Logo PRISMA -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none">
                    <x-application-logo class="w-10 h-10 rounded-xl shadow-md shadow-emerald-950/40 group-hover:scale-105 transition-transform duration-200" />
                    <div class="flex flex-col">
                        <span class="font-black text-xl sm:text-2xl tracking-tighter text-white group-hover:text-emerald-400 transition-colors">
                            PRISMA
                        </span>
                        <span class="text-[10px] font-semibold text-slate-300 tracking-widest uppercase -mt-1 hidden sm:block">
                            Repositori Modul Akademik
                        </span>
                    </div>
                </a>

                <!-- Nav Actions -->
                <nav class="flex items-center gap-3 sm:gap-4">
                    <a href="#katalog" class="text-sm font-medium text-slate-200 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5">
                        Katalog Kelas
                    </a>

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
                               class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <span>Ke Dasbor</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <span>Masuk</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </a>
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- HERO SECTION (Depth Layering: 3D Text Behind Campus Building) -->
    <section class="relative h-screen w-full overflow-hidden bg-slate-900 select-none">
        
        <!-- Lapis 1 (Paling Bawah, z-0): Background Utuh Kampus -->
        <img src="{{ asset('images/bg-campus.jpg') }}"
             alt="Gedung Kampus PRISMA"
             fetchpriority="high"
             class="absolute inset-0 w-full h-full object-cover object-top z-0" />

        <!-- Lapis 2 (Overlay & Gradient, z-10): Penyesuaian Suasana & Gradasi Bawah Gelap -->
        <div class="absolute inset-0 w-full h-full bg-slate-900/40 mix-blend-multiply z-10 pointer-events-none"></div>
        <div class="absolute inset-x-0 bottom-0 h-[55%] bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent z-10 pointer-events-none"></div>

        <!-- Lapis 3 (Teks Raksasa PRISMA, z-20): Berada di Belakang Atap Gedung -->
        {{-- Posisi teks dikunci ke garis atap foto: vh saat layar lebih tinggi dari 16:9, vw saat lebih lebar --}}
        <div class="absolute inset-x-0 z-20 text-center pointer-events-none select-none
                    top-[calc(17vh-3.2vw)]
                    md:[@media(max-aspect-ratio:16/9)]:top-[calc(12vh-2.55vw)]
                    [@media(min-aspect-ratio:16/9)]:top-[calc(max(4.5vw,5.75rem)-2.55vw)]">
            <h1 class="text-[19vw] md:text-[15vw] font-black text-white/90 tracking-tighter leading-none uppercase">
                PRISMA
            </h1>
        </div>

        <!-- Lapis 4 (Foreground Mask, z-30): Potongan Gedung Transparan -->
        <img src="{{ asset('images/bg-campus-cutout.png') }}"
             alt=""
             aria-hidden="true"
             fetchpriority="high"
             class="absolute inset-0 w-full h-full object-cover object-top z-30 pointer-events-none brightness-50 contrast-125" />

        <!-- Lapis 4.5 (Dark Bottom Gradient Ekstra, z-35): Alas Gelap Solid untuk Teks -->
        <div class="h-1/2 bottom-0 absolute w-full inset-x-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-transparent z-35 pointer-events-none"></div>

        <!-- Konten Utama (z-40): Headline, Deskripsi, & Tombol CTA -->
        <div class="relative z-40 h-full flex flex-col justify-end max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 sm:pb-36 lg:pb-32">
            <div class="max-w-3xl space-y-4">
                <!-- Sub-headline -->
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-slate-100 tracking-tight leading-snug">
                    Standar Baru Pembelajaran Informatika.
                </h2>

                <!-- Teks Deskripsi -->
                <p class="text-sm sm:text-base lg:text-lg text-slate-300 leading-relaxed max-w-2xl">
                    Pusat Repositori dan Integrasi Sayembara Modul Akademik. Akses modul praktikum terverifikasi, langsung dari dosen pengampu Anda.
                </p>

                <!-- Call to Action (CTA) Buttons -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-2">
                    @php
                        $targetLogin = auth()->check() ? ($dashboardUrl ?? route('dashboard')) : route('login');
                    @endphp
                    <a href="{{ $targetLogin }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm sm:text-base rounded-xl shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span>Masuk ke PRISMA</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    <a href="#katalog"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 border border-slate-300 text-slate-100 hover:bg-slate-800 font-semibold text-sm sm:text-base rounded-xl backdrop-blur-sm transition-colors duration-200">
                        <span>Jelajahi Katalog</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Glassmorphism Statistics (z-40, Menempel di Bagian Bawah Layar) -->
        <div class="absolute inset-x-0 bottom-0 z-40 backdrop-blur-md bg-white/10 border-t border-white/20 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4">
                <div class="grid grid-cols-3 divide-x divide-white/20">
                    <div class="px-2 sm:px-6 text-center sm:text-left">
                        <p class="text-xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">42+</p>
                        <p class="text-[10px] sm:text-xs text-slate-200 font-medium tracking-wide mt-0.5">Master Modul Tersertifikasi</p>
                    </div>
                    <div class="px-2 sm:px-6 text-center sm:text-left">
                        <p class="text-xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">15</p>
                        <p class="text-[10px] sm:text-xs text-slate-200 font-medium tracking-wide mt-0.5">Dosen Penyusun Aktif</p>
                    </div>
                    <div class="px-2 sm:px-6 text-center sm:text-left">
                        <p class="text-xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">300+</p>
                        <p class="text-[10px] sm:text-xs text-slate-200 font-medium tracking-wide mt-0.5">Mahasiswa Terhubung</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KATALOG SECTION (Standardized to @frontend-rules.md: Eye-Comfort Slate-50 / #F8F9FA Background, Anti-Slop Cards) -->
    <section id="katalog" class="bg-slate-50 text-slate-900 py-16 lg:py-24 border-t border-slate-200/80" x-data="{ search: '{{ addslashes($search ?? '') }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Section Header -->
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200/70 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-700"></span>
                    <span>Katalog Ruang Kelas</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Katalog Ruang Kelas Praktikum
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                    Pilih ruang kelas praktikum di bawah ini untuk melihat modul perkuliahan resmi yang telah disetujui oleh Program Studi.
                </p>
            </div>

            <!-- Search Bar Container -->
            <div class="max-w-2xl">
                <form method="GET" action="{{ url('/') }}#katalog" class="relative flex items-center bg-white rounded-xl border border-slate-300 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 shadow-sm transition-all duration-200">
                    <div class="pl-4 pr-2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search"
                        x-model="search"
                        placeholder="Cari kelas praktikum atau nama dosen pengampu..."
                        class="w-full py-3.5 pr-12 text-sm text-slate-900 placeholder-slate-400 bg-transparent border-0 focus:outline-none focus:ring-0"
                    />
                    <button 
                        type="button"
                        x-show="search.length > 0" 
                        x-cloak 
                        @click="search = ''; window.location.href = '{{ url('/') }}#katalog'" 
                        class="absolute right-4 text-slate-400 hover:text-slate-600 p-1 rounded-full focus:outline-none"
                        title="Bersihkan pencarian"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </form>
                <div x-show="search" x-cloak class="mt-2 text-xs text-slate-500">
                    Menyaring hasil berdasarkan: <span class="font-semibold text-emerald-800" x-text="search"></span>
                </div>
            </div>

            <!-- Empty State -->
            @if ($classes->isEmpty())
                <div class="bg-white rounded-xl border border-slate-200/80 p-12 text-center max-w-xl mx-auto shadow-sm my-8">
                    <div class="w-16 h-16 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center mx-auto mb-4 border border-slate-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Kelas Praktikum dengan Modul Resmi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed max-w-md mx-auto">
                        Saat ini belum ada ruang kelas yang memiliki modul berstatus disetujui oleh Kepala Jurusan. Silakan periksa kembali nanti.
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
                            class="group relative bg-white rounded-xl border border-slate-200/80 hover:border-slate-300 p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between"
                        >
                            <!-- Card Upper Content -->
                            <div>
                                <!-- Top Bar: Class Badge & Modules Count -->
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Kelas Praktikum
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $modulesCount }} Modul
                                    </span>
                                </div>

                                <!-- Class Name -->
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-800 transition-colors duration-150 line-clamp-2 mb-4 leading-snug">
                                    {{ $class->name }}
                                </h3>
                            </div>

                            <!-- Card Lower Content -->
                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-4">
                                <!-- Dosen Pengampu Info -->
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
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
                                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Dosen Pengampu</p>
                                        <p class="text-sm font-semibold text-slate-700 truncate" title="{{ $dosenName }}">
                                            {{ $dosenName }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Action Button: Lihat Daftar Modul -->
                                <a 
                                    href="{{ route('katalog.class', $class) }}" 
                                    class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl transition-colors duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
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
        </div>
    </section>

    <!-- Minimalist Footer (Compliant with @frontend-rules.md) -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-2.5">
                    <x-application-logo class="w-7 h-7 rounded-lg" />
                    <span class="text-sm font-bold text-slate-800">
                        PRISMA &bull; Repositori Modul Praktikum
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} PRISMA - Pusat Repositori dan Integrasi Sayembara Modul Akademik.
                </p>
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                    <a href="#katalog" class="hover:text-emerald-700 transition-colors">Katalog</a>
                    @auth
                        <a href="{{ $dashboardUrl ?? route('dashboard') }}" class="hover:text-emerald-700 transition-colors">Dasbor</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-emerald-700 transition-colors">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
