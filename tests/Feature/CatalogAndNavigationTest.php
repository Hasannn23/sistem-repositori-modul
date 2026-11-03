<?php

use App\Models\CourseClass;
use App\Models\Module;
use App\Models\User;

test('welcome page only displays course classes with approved modules', function () {
    $dosen = User::factory()->create(['name' => 'Prof. Andi Wijaya', 'role' => 'dosen']);

    // Class 1: Has approved module -> Should be visible on welcome
    $classWithApproved = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Pemrograman Berorientasi Objek',
        'referral_code' => 'PBO101',
    ]);
    Module::create([
        'course_class_id' => $classWithApproved->id,
        'title' => 'Modul 1: Kelas & Objek',
        'storage_type' => 'pdf',
        'file_path' => 'modules/pbo1.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    // Class 2: Only has draft module -> Should NOT be visible on welcome
    $classWithDraft = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Sistem Terdistribusi',
        'referral_code' => 'SIS202',
    ]);
    Module::create([
        'course_class_id' => $classWithDraft->id,
        'title' => 'Modul 1: RPC (Draft)',
        'storage_type' => 'pdf',
        'file_path' => 'modules/sis1.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    // Class 3: Has no modules at all -> Should NOT be visible on welcome
    $classEmpty = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Kriptografi',
        'referral_code' => 'KRP303',
    ]);

    $response = $this->get(url('/'));

    $response->assertStatus(200);
    $response->assertSee('Pemrograman Berorientasi Objek');
    $response->assertSee('Prof. Andi Wijaya');
    $response->assertSee('1 Modul');
    $response->assertSee(route('katalog.class', $classWithApproved));

    // Non-approved or empty classes should not be visible
    $response->assertDontSee('Sistem Terdistribusi');
    $response->assertDontSee('Kriptografi');
});

test('public katalog class page displays class details and approved modules', function () {
    $dosen = User::factory()->create(['name' => 'Dr. Rina Maryani', 'role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Jaringan Nirkabel',
        'referral_code' => 'WRL001',
    ]);

    $approvedModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Konfigurasi Access Point',
        'storage_type' => 'pdf',
        'file_path' => 'modules/wrl1.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    $draftModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 2: WPA3 Security (Draft)',
        'storage_type' => 'pdf',
        'file_path' => 'modules/wrl2.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $response = $this->get(route('katalog.class', $class));

    $response->assertStatus(200);
    $response->assertSee('Jaringan Nirkabel');
    $response->assertSee('Dr. Rina Maryani');
    $response->assertDontSee('WRL001');
    $response->assertSee('Modul 1: Konfigurasi Access Point');
    $response->assertDontSee('Modul 2: WPA3 Security (Draft)');
});

test('guest sees Login untuk Lihat Dokumen on katalog class page', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Keamanan Komputer',
        'referral_code' => 'SEC001',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Ethical Hacking',
        'storage_type' => 'pdf',
        'file_path' => 'modules/sec1.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    $response = $this->get(route('katalog.class', $class));

    $response->assertStatus(200);
    $response->assertSee('Login untuk Lihat Dokumen');
    $response->assertSee(route('login'));
    $response->assertDontSee('Lihat Modul');
});

test('authenticated user sees Lihat Modul on katalog class page', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Keamanan Komputer',
        'referral_code' => 'SEC001',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Ethical Hacking',
        'storage_type' => 'pdf',
        'file_path' => 'modules/sec1.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($student)->get(route('katalog.class', $class));

    $response->assertStatus(200);
    $response->assertSee('Lihat Modul');
    $response->assertSee(route('modules.show', $module));
    $response->assertDontSee('Login untuk Lihat Dokumen');
});

test('navigation bar displays role-based links and removes default dashboard link', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);
    $kajur = User::factory()->create(['role' => 'kajur']);

    // Check Mahasiswa Navigation
    $studentResponse = $this->actingAs($student)->get(route('student.dashboard'));
    $studentResponse->assertStatus(200);
    $studentResponse->assertSee(route('student.dashboard'));
    $studentResponse->assertSee('Kelas Saya');
    $studentResponse->assertDontSee(route('classes.index'));
    $studentResponse->assertDontSee('Kelola Kelas');
    $studentResponse->assertDontSee('Review Modul');

    // Check Dosen Navigation
    $dosenResponse = $this->actingAs($dosen)->get(route('classes.index'));
    $dosenResponse->assertStatus(200);
    $dosenResponse->assertSee('Kelola Kelas');
    $dosenResponse->assertSee('Modul Praktikum');
    $dosenResponse->assertDontSee(route('student.dashboard'));
    $dosenResponse->assertDontSee('Review Modul');

    // Check Kajur Navigation
    $kajurResponse = $this->actingAs($kajur)->get(route('kajur.modules.index'));
    $kajurResponse->assertStatus(200);
    $kajurResponse->assertSee('Review Modul');
    $kajurResponse->assertDontSee(route('student.dashboard'));
});
