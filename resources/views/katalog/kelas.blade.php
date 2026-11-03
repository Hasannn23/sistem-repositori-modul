<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Daftar Modul Praktikum untuk kelas {{ $courseClass->name }} - Sistem Repositori Modul.">

    <title>{{ $courseClass->name }} - Katalog Modul Praktikum</title>

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

    <!-- Main Content -->
    <main class="flex-grow py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Breadcrumbs / Back button -->
            <div>
                <a 
                    href="{{ url('/') }}" 
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-green-700 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Katalog Kelas</span>
                </a>
            </div>

            <!-- Class Detail Header Card -->
            <div class="bg-white rounded-3xl border border-gray-200/80 p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-green-700 to-emerald-500 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-sm shadow-green-600/30">
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
                            <div class="flex items-center flex-wrap gap-2 mb-1.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    {{ $modules->total() }} Modul Disetujui
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
                                {{ $courseClass->name }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-600">
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

                    @guest
                        <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 md:max-w-xs text-xs text-amber-800 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>
                                Mahasiswa atau civitas akademika harus <strong>login</strong> terlebih dahulu untuk dapat mengakses dan mengunduh berkas modul praktikum.
                            </p>
                        </div>
                    @endguest
                </div>
            </div>

            <!-- Modules List Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Daftar Modul Praktikum</h2>
                        <p class="text-sm text-gray-500">Materi modul pembelajaran yang telah berstatus disetujui (Approved).</p>
                    </div>
                </div>

                @if ($modules->isEmpty())
                    <div class="bg-white rounded-3xl border border-gray-200/80 p-12 text-center max-w-lg mx-auto shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4 border border-amber-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Belum Ada Modul Disetujui</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">
                            Modul praktikum untuk kelas ini masih dalam proses peninjauan oleh Ketua Jurusan atau belum diunggah.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($modules as $module)
                            @php
                                $isPdf = strtolower($module->storage_type) === 'pdf' || Str::endsWith(strtolower($module->file_path), '.pdf');
                                $isGdrive = strtolower($module->storage_type) === 'gdrive' || Str::contains($module->file_path, 'drive.google.com');
                            @endphp

                            <div class="group relative bg-white rounded-2xl border border-gray-200/90 hover:border-green-300 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                                <!-- Card Upper Content -->
                                <div>
                                    <!-- Top Badges -->
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                                                </svg>
                                                Google Drive
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                Dokumen
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Module Title -->
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-green-700 transition line-clamp-2 mb-2 leading-snug">
                                        {{ $module->title }}
                                    </h3>

                                    <p class="text-xs text-gray-400">
                                        Diperbarui: {{ $module->updated_at ? $module->updated_at->isoFormat('D MMMM Y') : '-' }}
                                    </p>
                                </div>

                                <!-- Card Lower Content: Action Button -->
                                <div class="mt-6 pt-4 border-t border-gray-100">
                                    @auth
                                        {{-- User sudah login: Arahkan ke halaman detail modul / streaming PDF --}}
                                        <a 
                                            href="{{ route('modules.show', $module) }}" 
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 active:bg-green-800 rounded-xl shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Modul</span>
                                        </a>
                                    @elseguest
                                        {{-- User belum login: Arahkan langsung ke route login --}}
                                        <a 
                                            href="{{ route('login') }}" 
                                            class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                                            title="Login terlebih dahulu untuk melihat dan mengunduh dokumen modul"
                                        >
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span>Login untuk Lihat Dokumen</span>
                                        </a>
                                    @endauth
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
