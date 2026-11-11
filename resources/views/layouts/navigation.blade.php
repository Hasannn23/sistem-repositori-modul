@php
    $user = Auth::user();
    $role = $user->role ?? '';
    $homeRoute = match($role) {
        'mahasiswa' => route('student.dashboard'),
        'dosen' => route('dashboard'),
        'kajur' => route('kajur.modules.index'),
        'super_admin' => route('dashboard'),
        default => url('/'),
    };
    $roleLabel = match($role) {
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen Pengampu',
        'kajur' => 'Kepala Jurusan',
        'super_admin' => 'Super Admin',
        default => 'Pengguna',
    };
    $userInitials = method_exists($user, 'initials') ? $user->initials() : strtoupper(substr($user->name ?? 'U', 0, 2));
@endphp

<div x-data="{ mobileUserMenu: false }">
    <!-- ==========================================
         1. DESKTOP LEFT SIDEBAR (md:flex)
         ========================================== -->
    <aside class="hidden md:flex md:flex-col md:w-64 md:fixed md:inset-y-0 bg-white border-r border-slate-200/80 z-40 select-none">
        <!-- Brand Logo Header -->
        <div class="h-16 px-6 flex items-center border-b border-slate-200/80 shrink-0">
            <a href="{{ $homeRoute }}" class="flex items-center gap-3 group focus:outline-none">
                <x-application-logo class="block h-9 w-auto group-hover:scale-105 transition-transform duration-200" />
                <div class="flex flex-col">
                    <span class="font-black text-lg tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">
                        PRISMA
                    </span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider -mt-1">
                        Repositori Modul
                    </span>
                </div>
            </a>
        </div>

        <!-- Sidebar Navigation Links -->
        <div class="flex-1 overflow-y-auto py-5 px-3.5 space-y-1">
            <div class="px-3 pb-2">
                <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Menu Utama</p>
            </div>

            @if ($role === 'mahasiswa')
                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('student.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('student.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>{{ __('Kelas Saya') }}</span>
                </a>

            @elseif ($role === 'dosen')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('classes.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('classes.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('classes.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>{{ __('Kelola Kelas') }}</span>
                </a>

                <a href="{{ route('sayembaras.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('sayembaras.*') || request()->routeIs('dosen.sayembara.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('sayembaras.*') || request()->routeIs('dosen.sayembara.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>{{ __('Sayembara Modul') }}</span>
                </a>

            @elseif ($role === 'kajur')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('kajur.courses.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('kajur.courses.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('kajur.courses.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>{{ __('Mata Kuliah') }}</span>
                </a>

                <a href="{{ route('kajur.modules.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('kajur.modules.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('kajur.modules.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>{{ __('Review Modul') }}</span>
                </a>

                <a href="{{ route('sayembaras.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('sayembaras.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('sayembaras.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>{{ __('Sayembara Modul') }}</span>
                </a>

            @elseif ($role === 'super_admin')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('faculties.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('faculties.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('faculties.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>{{ __('Kelola Fakultas') }}</span>
                </a>

                <a href="{{ route('departments.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('departments.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                    <span>{{ __('Kelola Jurusan') }}</span>
                </a>

                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>{{ __('Kelola Pengguna') }}</span>
                </a>

                <a href="{{ route('modules.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('modules.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('modules.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>{{ __('Modul Praktikum') }}</span>
                </a>
            @endif

            <div class="pt-4 mt-4 border-t border-slate-100">
                <div class="px-3 pb-2">
                    <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Pengaturan Akun</p>
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ __('Profile') }}</span>
                </a>
            </div>
        </div>

        <!-- Bottom User Profile & Logout Section -->
        <div class="p-4 border-t border-slate-200/80 bg-slate-50/60 shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-200/80">
                    {{ $userInitials }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm text-slate-900 truncate">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ $roleLabel }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 rounded-xl transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ==========================================
         2. MOBILE TOP HEADER BAR (< md)
         ========================================== -->
    <div class="md:hidden sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 h-14 flex items-center justify-between">
        <a href="{{ $homeRoute }}" class="flex items-center gap-2.5 focus:outline-none">
            <x-application-logo class="block h-8 w-auto" />
            <span class="font-black text-base tracking-tight text-slate-900">PRISMA</span>
        </a>

        <!-- Mobile Profile & Logout Trigger -->
        <div class="relative">
            <button @click="mobileUserMenu = !mobileUserMenu"
                    type="button"
                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-semibold text-slate-700 focus:outline-none">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-[11px] flex items-center justify-center border border-emerald-200">
                    {{ $userInitials }}
                </span>
                <span class="max-w-[110px] truncate">{{ $user->name }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="mobileUserMenu"
                 @click.outside="mobileUserMenu = false"
                 x-transition
                 style="display: none;"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-200/80 py-2 z-50">
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ==========================================
         3. MOBILE BOTTOM NAVIGATION BAR (< md)
         ========================================== -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t border-slate-200/90 shadow-lg select-none">
        <div class="flex items-center justify-around h-16 px-2">
            @if ($role === 'mahasiswa')
                <a href="{{ route('student.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('student.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-[11px] leading-none">{{ __('Kelas Saya') }}</span>
                </a>

            @elseif ($role === 'dosen')
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('dashboard') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[11px] leading-none">{{ __('Dashboard') }}</span>
                </a>

                <a href="{{ route('classes.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('classes.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-[11px] leading-none">{{ __('Kelola Kelas') }}</span>
                </a>

                <a href="{{ route('sayembaras.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('sayembaras.*') || request()->routeIs('dosen.sayembara.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span class="text-[11px] leading-none">Sayembara</span>
                </a>

            @elseif ($role === 'kajur')
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('dashboard') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[11px] leading-none">Dashboard</span>
                </a>

                <a href="{{ route('kajur.courses.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('kajur.courses.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-[11px] leading-none">Matkul</span>
                </a>

                <a href="{{ route('kajur.modules.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('kajur.modules.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="text-[11px] leading-none">Review</span>
                </a>

                <a href="{{ route('sayembaras.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('sayembaras.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span class="text-[11px] leading-none">Sayembara</span>
                </a>

            @elseif ($role === 'super_admin')
                <a href="{{ route('faculties.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('faculties.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-[11px] leading-none">Fakultas</span>
                </a>

                <a href="{{ route('departments.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('departments.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                    <span class="text-[11px] leading-none">Jurusan</span>
                </a>

                <a href="{{ route('users.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('users.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-[11px] leading-none">Pengguna</span>
                </a>
            @endif

            <a href="{{ route('profile.edit') }}"
               class="flex flex-col items-center justify-center flex-1 py-1.5 gap-1 {{ request()->routeIs('profile.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[11px] leading-none">{{ __('Profile') }}</span>
            </a>
        </div>
    </nav>
</div>
