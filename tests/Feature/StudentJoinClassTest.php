<?php

use App\Models\CourseClass;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('unauthenticated users cannot access student routes', function () {
    $class = CourseClass::create([
        'dosen_id' => User::factory()->create(['role' => 'dosen'])->id,
        'name' => 'Pemrograman Web',
        'referral_code' => 'WEB001',
    ]);

    $this->get(route('student.dashboard'))->assertRedirect(route('login'));
    $this->post(route('student.join'), ['referral_code' => 'WEB001'])->assertRedirect(route('login'));
    $this->get(route('student.class.show', $class))->assertRedirect(route('login'));
});

test('user and course class pivot relationships work correctly', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Jaringan Komputer',
        'referral_code' => 'NET101',
    ]);

    $student->joinedClasses()->attach($class->id);

    expect($student->joinedClasses)->toHaveCount(1);
    expect($student->joinedClasses->first()->id)->toBe($class->id);
    expect($class->students)->toHaveCount(1);
    expect($class->students->first()->id)->toBe($student->id);
});

test('student can view dashboard with empty state when no classes joined', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dasbor Mahasiswa');
    $response->assertSee('Gabung Kelas Baru');
    $response->assertSee('Belum Ada Kelas yang Diikuti');
});

test('student can join class using valid referral code', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Kecerdasan Buatan',
        'referral_code' => 'AI2026',
    ]);

    $response = $this->actingAs($student)->post(route('student.join'), [
        'referral_code' => 'ai2026', // Test case insensitivity
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_class_user', [
        'course_class_id' => $class->id,
        'user_id' => $student->id,
    ]);

    // Student dashboard now displays the class
    $dashResponse = $this->actingAs($student)->get(route('student.dashboard'));
    $dashResponse->assertStatus(200);
    $dashResponse->assertSee('Kecerdasan Buatan');
    $dashResponse->assertSee('AI2026');
    $dashResponse->assertSee(route('student.class.show', $class));
});

test('joining class fails if referral code is missing', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);

    $response = $this->actingAs($student)->post(route('student.join'), [
        'referral_code' => '',
    ]);

    $response->assertSessionHasErrors(['referral_code']);
});

test('joining class fails if referral code is not found', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);

    $response = $this->actingAs($student)->post(route('student.join'), [
        'referral_code' => 'NOTFOUND99',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Kelas tidak ditemukan.');
    $this->assertDatabaseCount('course_class_user', 0);
});

test('joining class fails if student has already joined the class', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Algoritma Pemrograman',
        'referral_code' => 'ALGO01',
    ]);

    $student->joinedClasses()->attach($class->id);

    $response = $this->actingAs($student)->post(route('student.join'), [
        'referral_code' => 'ALGO01',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect($student->joinedClasses()->count())->toBe(1);
});

test('student cannot view class detail if not enrolled', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Sistem Operasi',
        'referral_code' => 'OS2026',
    ]);

    $response = $this->actingAs($student)->get(route('student.class.show', $class));
    $response->assertStatus(403);
});

test('enrolled student can view class detail and only approved modules', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['name' => 'Dr. Budi Santoso', 'role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Struktur Data & Algoritma',
        'referral_code' => 'SDA101',
    ]);

    // Student joins the class
    $student->joinedClasses()->attach($class->id);

    // Create approved module
    $approvedModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Pengenalan Pointer',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modul1.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    // Create draft module
    $draftModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 2: Linked List (Draft)',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modul2.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    // Create waiting review module
    $reviewModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 3: Binary Tree (Waiting Review)',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modul3.pdf',
        'is_locked' => true,
        'status' => 'waiting_review',
    ]);

    $response = $this->actingAs($student)->get(route('student.class.show', $class));

    $response->assertStatus(200);
    $response->assertSee('Struktur Data & Algoritma');
    $response->assertSee('Dr. Budi Santoso');
    $response->assertSee('Modul 1: Pengenalan Pointer');
    $response->assertSee('Lihat Modul');

    // Non-approved modules must not be visible to student
    $response->assertDontSee('Modul 2: Linked List (Draft)');
    $response->assertDontSee('Modul 3: Binary Tree (Waiting Review)');
});

test('enrolled student can access approved module file via module show route', function () {
    Storage::fake('public');

    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Pemrograman Berorientasi Objek',
        'referral_code' => 'PBO001',
    ]);

    $student->joinedClasses()->attach($class->id);

    $dummyFile = 'modules/test_module.pdf';
    Storage::disk('public')->put($dummyFile, '%PDF-1.4 test file content');

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul PBO 1',
        'storage_type' => 'pdf',
        'file_path' => $dummyFile,
        'is_locked' => false,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($student)->get(route('modules.show', $module));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('student cannot access unapproved draft module file via module show route', function () {
    Storage::fake('public');

    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Rekayasa Perangkat Lunak',
        'referral_code' => 'RPL001',
    ]);

    $dummyFile = 'modules/rpl_module.pdf';
    Storage::disk('public')->put($dummyFile, '%PDF-1.4 test file content');

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul RPL 1 Draft',
        'storage_type' => 'pdf',
        'file_path' => $dummyFile,
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($student)->get(route('modules.show', $module));
    $response->assertStatus(403);
});
