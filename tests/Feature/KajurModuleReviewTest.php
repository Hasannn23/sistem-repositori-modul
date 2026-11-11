<?php

use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\User;

test('unauthenticated user cannot access kajur module routes', function () {
    $response = $this->get(route('kajur.modules.index'));
    $response->assertRedirect(route('login'));
});

test('non-kajur users cannot access kajur module review routes', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $response = $this->actingAs($dosen)->get(route('kajur.modules.index'));
    $response->assertStatus(403);
});

test('kajur can view waiting_review modules only from their department', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $deptTI = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);
    $deptSI = Department::create(['faculty_id' => $faculty->id, 'name' => 'Sistem Informasi']);

    $kajurTI = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $deptTI->id,
    ]);

    $dosenTI = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $deptTI->id,
        'name' => 'Dosen TI Budi',
    ]);

    $dosenSI = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $deptSI->id,
        'name' => 'Dosen SI Cindy',
    ]);

    $classTI = CourseClass::create([
        'dosen_id' => $dosenTI->id,
        'name' => 'Kelas Pemrograman TI',
        'referral_code' => 'TI2026',
    ]);

    $classSI = CourseClass::create([
        'dosen_id' => $dosenSI->id,
        'name' => 'Kelas Basis Data SI',
        'referral_code' => 'SI2026',
    ]);

    $moduleWaitingTI = Module::create([
        'course_class_id' => $classTI->id,
        'title' => 'Modul Menunggu Review TI',
        'storage_type' => 'pdf',
        'file_path' => 'modules/ti_modul.pdf',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $moduleDraftTI = Module::create([
        'course_class_id' => $classTI->id,
        'title' => 'Modul Draft TI Tidak Tampil',
        'storage_type' => 'pdf',
        'file_path' => 'modules/draft_ti.pdf',
        'status' => 'draft',
        'is_locked' => true,
    ]);

    $moduleWaitingSI = Module::create([
        'course_class_id' => $classSI->id,
        'title' => 'Modul Jurusan SI Jangan Tampil',
        'storage_type' => 'pdf',
        'file_path' => 'modules/si_modul.pdf',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajurTI)->get(route('kajur.modules.index'));

    $response->assertStatus(200);
    $response->assertSee('Modul Menunggu Review TI');
    $response->assertSee('Dosen TI Budi');
    $response->assertSee('Kelas Pemrograman TI');
    $response->assertSee('Review Modul');

    // Should NOT see other department module or draft module
    $response->assertDontSee('Modul Draft TI Tidak Tampil');
    $response->assertDontSee('Modul Jurusan SI Jangan Tampil');
    $response->assertDontSee('Dosen SI Cindy');
});

test('kajur can access module review page with author profile card and meetings', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $dept = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Elektro']);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $dept->id,
    ]);

    $dosen = User::factory()->create([
        'name' => 'Prof. Alan Turing, Ph.D.',
        'role' => 'dosen',
        'department_id' => $dept->id,
        'nidn' => '1234567890',
    ]);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Komputasi',
        'referral_code' => 'KOMP123',
    ]);

    $module = Module::create([
        'author_id' => $dosen->id,
        'course_class_id' => $class->id,
        'title' => 'Modul Teori Komputasi & Automata',
        'storage_type' => 'lms',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajur)->get(route('kajur.modules.review', $module));

    $response->assertOk();
    $response->assertSee('Prof. Alan Turing, Ph.D.');
    $response->assertSee('1234567890');
    $response->assertSee('Tetapkan Master');
    $response->assertSee('Kembalikan/Revisi');
    $response->assertSee('Kembali ke Antrean Pengajuan');
});

test('kajur approving module sets it as master and closes associated sayembara', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Sains']);
    $dept = Department::create(['faculty_id' => $faculty->id, 'name' => 'Matematika']);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $dept->id,
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $dept->id,
    ]);

    $course = \App\Models\Course::create([
        'department_id' => $dept->id,
        'code' => 'MAT-101',
        'name' => 'Kalkulus Dasar',
        'credit_hours' => 3,
    ]);

    $sayembara = \App\Models\Sayembara::create([
        'department_id' => $dept->id,
        'course_id' => $course->id,
        'title' => 'Sayembara Modul Kalkulus',
        'description' => 'Penyusunan draf master modul kalkulus dasar.',
        'status' => 'open',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->toDateString(),
    ]);

    $module = Module::create([
        'author_id' => $dosen->id,
        'course_id' => $course->id,
        'sayembara_id' => $sayembara->id,
        'title' => 'Modul Kalkulus Integral',
        'storage_type' => 'lms',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajur)->post(route('kajur.modules.approve', $module));

    $response->assertRedirect(route('kajur.modules.index'));
    $response->assertSessionHas('success');

    $module->refresh();
    $sayembara->refresh();

    expect($module->status)->toBe('approved');
    expect($module->is_master)->toBeTrue();
    expect($module->is_locked)->toBeFalse();
    expect($module->kajur_id)->toBe($kajur->id);
    expect($sayembara->status)->toBe('closed');
});

test('kajur can approve a module', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $dept = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Elektro']);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $dept->id,
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $dept->id,
    ]);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Rangkaian Listrik',
        'referral_code' => 'ELK123',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Hukum Ohm',
        'storage_type' => 'pdf',
        'file_path' => 'modules/ohm.pdf',
        'status' => 'waiting_review',
        'kajur_notes' => 'Catatan revisi sebelumnya yang harus dihapus saat ACC',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajur)->post(route('kajur.modules.approve', $module->id));

    $response->assertSessionHas('success');

    $module->refresh();
    expect($module->status)->toBe('approved');
    expect($module->is_locked)->toBeFalse();
    expect($module->kajur_notes)->toBeNull();
    expect($module->kajur_id)->toBe($kajur->id);
});

test('kajur can reject a module with revision notes', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $dept = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Komputer']);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $dept->id,
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $dept->id,
    ]);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Embedded System',
        'referral_code' => 'EMB123',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Arduino & Sensor Ultrasonic',
        'storage_type' => 'pdf',
        'file_path' => 'modules/arduino.pdf',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajur)->post(route('kajur.modules.reject', $module->id), [
        'kajur_notes' => 'Tolong tambahkan skema rangkaian pinout mikrokontroler.',
    ]);

    $response->assertSessionHas('success');

    $module->refresh();
    expect($module->status)->toBe('revision');
    expect($module->kajur_notes)->toBe('Tolong tambahkan skema rangkaian pinout mikrokontroler.');
    expect($module->kajur_id)->toBe($kajur->id);
});

test('kajur reject requires kajur_notes', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $dept = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Komputer']);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $dept->id,
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $dept->id,
    ]);

    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Jaringan Komputer',
        'referral_code' => 'NET123',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Routing OSPF',
        'storage_type' => 'pdf',
        'file_path' => 'modules/ospf.pdf',
        'status' => 'waiting_review',
        'is_locked' => true,
    ]);

    $response = $this->actingAs($kajur)->post(route('kajur.modules.reject', $module->id), [
        'kajur_notes' => '',
    ]);

    $response->assertSessionHasErrors('kajur_notes');

    $module->refresh();
    expect($module->status)->toBe('waiting_review');
});
