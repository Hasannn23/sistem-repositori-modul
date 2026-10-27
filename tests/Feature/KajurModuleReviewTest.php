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
    $response->assertSee('Lihat PDF');
    $response->assertSee('Setujui (ACC)');
    $response->assertSee('Kembalikan (Revisi)');

    // Should NOT see other department module or draft module
    $response->assertDontSee('Modul Draft TI Tidak Tampil');
    $response->assertDontSee('Modul Jurusan SI Jangan Tampil');
    $response->assertDontSee('Dosen SI Cindy');
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
