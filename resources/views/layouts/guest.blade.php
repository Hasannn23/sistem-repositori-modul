<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PRISMA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 bg-slate-50 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-slate-50">
            <!-- Back to Landing Page Link -->
            <div class="w-full sm:max-w-md mb-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-colors duration-200 group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>

            <!-- Brand Identity -->
            <div class="flex flex-col items-center text-center mb-6">
                <a href="{{ url('/') }}" class="flex flex-col items-center gap-2 group focus:outline-none">
                    <x-application-logo class="w-14 h-14 group-hover:scale-105 transition-transform duration-200" />
                    <div>
                        <span class="font-black text-2xl tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">
                            PRISMA
                        </span>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">
                            Pusat Repositori dan Integrasi Sayembara Modul Akademik
                        </p>
                    </div>
                </a>
            </div>

            <!-- Auth Form Container -->
            <div class="w-full sm:max-w-md px-6 py-7 bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
