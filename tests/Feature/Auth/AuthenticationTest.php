<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('students can authenticate using SIAKAD bridge API with NIM', function () {
    $faculty = \App\Models\Faculty::create(['name' => 'Fakultas Ilmu Tarbiyah dan Keguruan']);
    $dept = \App\Models\Department::create([
        'faculty_id' => $faculty->id,
        'name' => 'Informatika',
    ]);

    \Illuminate\Support\Facades\Http::fake([
        '*/api/login' => \Illuminate\Support\Facades\Http::response([
            'status' => true,
            'data' => [
                'nim' => '2108101001',
                'nama' => 'Ahmad Fulan',
                'email' => 'ahmad@uinssc.ac.id',
                'program_studi_nama' => 'Informatika',
            ],
        ], 200),
    ]);

    $response = $this->post('/login', [
        'nim' => '2108101001',
        'password' => 'rahasia123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $createdUser = User::where('nim', '2108101001')->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->name)->toBe('Ahmad Fulan')
        ->and($createdUser->role)->toBe('mahasiswa')
        ->and($createdUser->department_id)->toBe($dept->id);
});

test('authentication fails when SIAKAD API returns status false', function () {
    \Illuminate\Support\Facades\Http::fake([
        '*/api/login' => \Illuminate\Support\Facades\Http::response([
            'status' => false,
            'message' => 'NIM atau password salah',
        ], 200),
    ]);

    $response = $this->post('/login', [
        'nim' => '2108109999',
        'password' => 'wrong',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['nim']);
});

