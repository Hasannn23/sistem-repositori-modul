<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\User;

function setupCourseMasterModuleScenario(): array
{
    $faculty = Faculty::create(['name' => 'Fakultas Teknik & Sains']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);

    $kajur = User::factory()->create([
        'role'          => 'kajur',
        'department_id' => $department->id,
        'name'          => 'Dr. Hendra Gunawan, M.T.',
    ]);

    $dosen = User::factory()->create([
        'role'          => 'dosen',
        'department_id' => $department->id,
        'name'          => 'Budi Prasetyo, M.Kom.',
        'nidn'          => '0012058801',
    ]);

    $student = User::factory()->create([
        'role'          => 'mahasiswa',
        'department_id' => $department->id,
        'name'          => 'Ahmad Rifai',
        'nim'           => '2026110001',
    ]);

    $courseWithMaster = Course::create([
        'department_id' => $department->id,
        'code'          => 'IF201',
        'name'          => 'Algoritma & Pemrograman',
        'credit_hours'  => 3,
        'description'   => 'Dasar algoritma',
    ]);

    $courseWithoutMaster = Course::create([
        'department_id' => $department->id,
        'code'          => 'IF202',
        'name'          => 'Jaringan Komputer Lanjut',
        'credit_hours'  => 3,
        'description'   => 'Konsep routing dan switching',
    ]);

    // Master module for IF201
    $masterModule = Module::create([
        'author_id'       => $dosen->id,
        'course_id'       => $courseWithMaster->id,
        'title'           => 'Master Modul Resmi Algoritma Pemrograman',
        'description'     => 'Panduan praktikum resmi kurikulum 2026',
        'storage_type'    => 'lms',
        'is_master'       => true,
        'status'          => 'approved',
        'is_locked'       => false,
    ]);

    // Class 1 (has master module via course)
    $class1 = CourseClass::create([
        'course_id'     => $courseWithMaster->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas Alpro A',
        'referral_code' => 'ALP101',
    ]);

    // Class 2 (no master module yet)
    $class2 = CourseClass::create([
        'course_id'     => $courseWithoutMaster->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas Jarkom B',
        'referral_code' => 'JAR202',
    ]);

    // Enroll student to both classes
    $student->joinedClasses()->attach([$class1->id, $class2->id]);

    return compact('faculty', 'department', 'kajur', 'dosen', 'student', 'courseWithMaster', 'courseWithoutMaster', 'masterModule', 'class1', 'class2');
}

test('model Course has masterModule relation and CourseClass belongs to Course', function () {
    $s = setupCourseMasterModuleScenario();

    expect($s['courseWithMaster']->masterModule)->not->toBeNull()
        ->and($s['courseWithMaster']->masterModule->id)->toBe($s['masterModule']->id)
        ->and($s['courseWithoutMaster']->masterModule)->toBeNull()
        ->and($s['class1']->course->id)->toBe($s['courseWithMaster']->id);
});

test('dosen class index displays course as main title, class name as subtitle, and correct master module CTA button', function () {
    $s = setupCourseMasterModuleScenario();

    $response = $this->actingAs($s['dosen'])->get(route('classes.index'));

    $response->assertOk();
    // Verify total mahasiswa stat instead of total modul
    $response->assertSee('Total Mahasiswa Terdaftar');
    $response->assertDontSee('Total Modul Terunggah');

    // Verify main title and subtitle
    $response->assertSee('Algoritma & Pemrograman');
    $response->assertSee('Kelas Alpro A');
    $response->assertSee('Jaringan Komputer Lanjut');
    $response->assertSee('Kelas Jarkom B');

    // Class 1 has master module -> Buka LMS Modul button
    $response->assertSee('Buka LMS Modul');
    $response->assertSee(route('modules.lms', $s['masterModule']->id));

    // Class 2 does not have master module -> Disabled waiting badge
    $response->assertSee('Menunggu Modul Resmi Prodi');

    // Verify modal dropdown lists courses
    $response->assertSee('IF201 - Algoritma & Pemrograman');
    $response->assertSee('IF202 - Jaringan Komputer Lanjut');
});

test('dosen can create new class selecting course from dropdown', function () {
    $s = setupCourseMasterModuleScenario();

    $response = $this->actingAs($s['dosen'])->post(route('classes.store'), [
        'course_id' => $s['courseWithMaster']->id,
        'name'      => 'Kelas Alpro C Malam',
    ]);

    $response->assertRedirect(route('classes.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_classes', [
        'course_id' => $s['courseWithMaster']->id,
        'dosen_id'  => $s['dosen']->id,
        'name'      => 'Kelas Alpro C Malam',
    ]);
});

test('student class detail inherits master module and shows single prominent CTA or clean empty state', function () {
    $s = setupCourseMasterModuleScenario();

    // 1. Visit Class 1 (has master module via course)
    $response1 = $this->actingAs($s['student'])->get(route('student.class.show', $s['class1']));
    $response1->assertOk();
    $response1->assertSee('MASTER MODUL RESMI PRODI');
    $response1->assertSee('Master Modul Resmi Algoritma Pemrograman');
    $response1->assertSee('Buka LMS / Mulai Belajar');
    $response1->assertSee(route('modules.lms', $s['masterModule']->id));
    // Must NOT have multiple module counters or upload cards
    $response1->assertDontSee('Total Modul Tersedia');

    // 2. Visit Class 2 (no master module yet)
    $response2 = $this->actingAs($s['student'])->get(route('student.class.show', $s['class2']));
    $response2->assertOk();
    $response2->assertSee('Modul praktikum untuk mata kuliah ini sedang dalam penyusunan oleh Program Studi.');
    $response2->assertDontSee('Buka LMS / Mulai Belajar');
});

test('lms reader show_lms does not contain Unduh PDF Utama button in header', function () {
    $s = setupCourseMasterModuleScenario();

    $response = $this->actingAs($s['student'])->get(route('modules.lms', $s['masterModule']->id));

    $response->assertOk();
    $response->assertDontSee('Unduh PDF Utama');
    $response->assertDontSee('Buka Tautan Utama');
    $response->assertSee('Master Modul Resmi Algoritma Pemrograman');
});

