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
        $loginInput = $request->input('nim') ?? $request->input('email');

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (empty($loginInput)) {
            throw ValidationException::withMessages([
                'email' => 'NIM atau Email wajib diisi.',
                'nim'   => 'NIM atau Email wajib diisi.',
            ]);
        }

        $credentials = [
            'nim'      => $loginInput,
            'password' => $request->input('password'),
        ];

        // Cek login lokal terlebih dahulu (misal untuk Super Admin / Dosen / Kajur / Pengguna lokal)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';
        if (Auth::attempt([$field => $loginInput, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            // Ambil URL dari .env (tambahkan endpoint spesifik yang dipakai di Postman, misal: /api/login)
            // TODO: Pastikan path endpoint di bawah ini sesuai dengan URL yang diketik di Postman!
            $endpoint = rtrim((string) env('SSO_BRIDGE_URL'), '/') . '/api/login'; 

            $response = Http::timeout(env('SSO_TIMEOUT', 10))->post($endpoint, [
                'username' => $credentials['nim'], // Sesuaikan key ini ('username' atau 'nim') dengan yang dipakai saat send di Postman
                'password' => $credentials['password'],
            ]);

            // Cek apakah request sukses HTTP 200 DAN status di JSON-nya adalah true
            if ($response->successful() && $response->json('status') === true) {
                $userData = $response->json('data');

                // Cari Department ID berdasarkan nama prodi dari API
                $department = ! empty($userData['program_studi_nama'])
                    ? \App\Models\Department::where('name', 'LIKE', '%' . $userData['program_studi_nama'] . '%')->first()
                    : null;
                
                // Simpan atau update user ke database lokal
                $user = User::updateOrCreate(
                    ['nim' => $userData['nim']],
                    [
                        'name'          => $userData['nama'],
                        'email'         => $userData['email_kampus'] ?? $userData['email'] ?? ($userData['nim'] . '@' . env('SSO_EMAIL_FALLBACK_DOMAIN', 'sso.uinssc.ac.id')),
                        'password'      => Hash::make(Str::random(32)), // Password asli tetap aman di SIAKAD
                        'role'          => 'mahasiswa', // Set default mahasiswa berdasarkan jenjang S1 dari API
                        'department_id' => $department ? $department->id : null,
                    ]
                );

                // Lakukan login manual
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard', absolute: false));
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Abaikan error eksternal agar dilanjutkan ke pesan gagal validasi
        }

        // Jika gagal (salah password / nim tidak ditemukan di SIAKAD)
        $errorKey = $request->filled('nim') ? 'nim' : 'email';
        throw ValidationException::withMessages([
            $errorKey => 'NIM atau Password tidak cocok dengan data SIAKAD.',
            'nim'     => 'NIM atau Password tidak cocok dengan data SIAKAD.',
        ]);
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
