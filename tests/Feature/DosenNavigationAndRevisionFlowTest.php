<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\Sayembara;
use App\Models\User;

function setupNavigationAndRevisionScenario(): array
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

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'IF302',
        'name'          => 'Struktur Data & Algoritma',
        'credit_hours'  => 3,
        'description'   => 'Mata kuliah struktur data dasar',
    ]);

    $courseClass = CourseClass::create([
        'dosen_id'      => $dosen->id,
        'course_id'     => $course->id,
        'name'          => 'Kelas IF-A',
        'referral_code' => 'IF-A-2026',
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'created_by'    => $kajur->id,
        'title'         => 'Sayembara Modul Praktikum Struktur Data',
        'description'   => 'Penyusunan modul kurikulum 2026',
        'status'        => 'open',
        'start_date'    => now()->subDays(2),
        'end_date'      => now()->addDays(14),
    ]);

    $module = Module::create([
        'author_id'       => $dosen->id,
        'course_id'       => $course->id,
        'sayembara_id'    => $sayembara->id,
        'course_class_id' => $courseClass->id,
        'title'           => 'Draf Modul Tree & Graph',
        'description'     => 'Modul praktikum struktur data lanjut',
        'storage_type'    => 'lms',
        'is_locked'       => true,
        'is_master'       => false,
        'status'          => 'waiting_review',
    ]);

    return compact('faculty', 'department', 'kajur', 'dosen', 'course', 'courseClass', 'sayembara', 'module');
}

test('dosen navigation navbar does not contain Modul Praktikum and contains Dashboard, Kelola Kelas, Sayembara Modul', function () {
    $s = setupNavigationAndRevisionScenario();

    $response = $this->actingAs($s['dosen'])->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Kelola Kelas');
    $response->assertSee('Sayembara Modul');
    // Ensure "Modul Praktikum" as a navigation link is not in the navbar
    $response->assertDontSee('>Modul Praktikum<', false);
    $response->assertDontSee('route(\'modules.index\')', false);
});

test('dosen attempting to access modules.create is redirected to sayembaras.index', function () {
    $s = setupNavigationAndRevisionScenario();

    $response = $this->actingAs($s['dosen'])->get(route('modules.create'));

    $response->assertRedirect(route('sayembaras.index'));
    $response->assertSessionHas('info');
});

test('kajur can reject module with revision_note and module status becomes revision', function () {
    $s = setupNavigationAndRevisionScenario();

    $response = $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.reject', $s['module']), [
            'revision_note' => 'Pertemuan 5 mohon ditambahkan contoh implementasi Binary Search Tree.',
        ]);

    $response->assertRedirect(route('kajur.modules.index'));
    $response->assertSessionHas('success');

    $s['module']->refresh();
    expect($s['module']->status)->toBe('revision')
        ->and($s['module']->is_locked)->toBeFalse()
        ->and($s['module']->revision_note)->toBe('Pertemuan 5 mohon ditambahkan contoh implementasi Binary Search Tree.')
        ->and($s['module']->kajur_notes)->toBe('Pertemuan 5 mohon ditambahkan contoh implementasi Binary Search Tree.');
});

test('sayembara show page displays revision badge, kajur revision alert, and Lanjut Edit di LMS Builder button', function () {
    $s = setupNavigationAndRevisionScenario();

    // Set module to revision
    $s['module']->update([
        'status'        => 'revision',
        'is_locked'     => false,
        'revision_note' => 'Catatan revisi: Mohon perbaiki studi kasus Pertemuan 3.',
        'kajur_notes'   => 'Catatan revisi: Mohon perbaiki studi kasus Pertemuan 3.',
    ]);

    $response = $this->actingAs($s['dosen'])->get(route('sayembaras.show', $s['sayembara']));

    $response->assertOk();
    $response->assertSee('Revisi');
    $response->assertSee('Catatan Revisi dari Kajur:');
    $response->assertSee('Catatan revisi: Mohon perbaiki studi kasus Pertemuan 3.');
    $response->assertSee('Lanjut Edit di LMS Builder');
    $response->assertSee(route('dosen.modules.builder', $s['module']->id));
});
