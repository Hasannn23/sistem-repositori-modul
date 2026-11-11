<x-guest-layout>
    <!-- Header Title -->
    <div class="mb-6">
        <h1 class="text-lg font-bold text-slate-900">Masuk ke Akun Anda</h1>
        <p class="text-xs text-slate-600 mt-1">Gunakan NIM (untuk Mahasiswa) atau alamat Email akademis Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- NIM / Email Address -->
        <div>
            <x-input-label for="email" :value="__('NIM / Email')" class="text-xs font-semibold tracking-wider uppercase text-slate-700" />
            <input id="email"
                   class="block mt-1.5 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm text-slate-900 py-2.5 px-3.5 shadow-2xs"
                   type="text"
                   name="email"
                   value="{{ old('email') ?? old('nim') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="Masukkan NIM (Mahasiswa) atau Email" />
            <x-input-error :messages="$errors->get('email') ?: $errors->get('nim')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs font-semibold tracking-wider uppercase text-slate-700" />
            <input id="password"
                   class="block mt-1.5 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm text-slate-900 py-2.5 px-3.5 shadow-2xs"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-700 shadow-2xs focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-xs text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-medium text-slate-600 hover:text-emerald-700 transition-colors focus:outline-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit & Back Action -->
        <div class="pt-2 space-y-3">
            <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                {{ __('Log in') }}
            </button>

            <a href="{{ url('/') }}" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-xl transition-colors duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </form>
</x-guest-layout>
