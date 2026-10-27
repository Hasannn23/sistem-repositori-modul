<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request using SIAKAD API.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Ambil input email (atau NIM) dan password dari form login
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            // 2. Buat simulasi HTTP POST request ke API SIAKAD menggunakan Illuminate\Support\Facades\Http
            // TODO: Ganti URL dengan endpoint SIAKAD asli
            $siakadEndpoint = 'https://api.kampus.ac.id/v1/auth/login';

            $response = Http::timeout(10)->post($siakadEndpoint, [
                'email'    => $credentials['email'],
                'password' => $credentials['password'],
            ]);

            // 4. Jika API mengembalikan error (status bukan 200 / kredensial salah)
            if (! $response->successful()) {
                throw ValidationException::withMessages([
                    'email' => 'Kredensial tidak cocok dengan data SIAKAD',
                ]);
            }

            // 3. Ambil data user dari respons API (misal: nama, email, role, department_id)
            $userData = $response->json('data') ?? $response->json();

            if (empty($userData)) {
                throw ValidationException::withMessages([
                    'email' => 'Kredensial tidak cocok dengan data SIAKAD',
                ]);
            }

            // Gunakan User::updateOrCreate() untuk mencari user berdasarkan email.
            // Jika belum ada, buat otomatis di database lokal (dengan password dummy karena password asli ada di SIAKAD).
            $user = User::updateOrCreate(
                [
                    'email' => $userData['email'] ?? $credentials['email'],
                ],
                [
                    'name'          => $userData['nama'] ?? $userData['name'] ?? $credentials['email'],
                    'role'          => $userData['role'] ?? 'mahasiswa',
                    'department_id' => $userData['department_id'] ?? null,
                    'password'      => Hash::make(Str::random(32)), // Password dummy karena autentikasi dikelola SIAKAD
                ]
            );

            // Login-kan user tersebut secara manual menggunakan Auth::login($user)
            Auth::login($user, $request->boolean('remember'));

            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Redirect ke route 'dashboard'
            return redirect()->intended(route('dashboard', absolute: false));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Tangani error koneksi / timeout ke server SIAKAD
            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak cocok dengan data SIAKAD',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
