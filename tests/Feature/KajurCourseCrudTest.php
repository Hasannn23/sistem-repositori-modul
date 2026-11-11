<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Helper: Setup departments and Kajur users
|--------------------------------------------------------------------------
*/
function createKajurCourseScenario(): array
{
    $faculty = Faculty::create(['name' => 'Fakultas Sains dan Teknologi']);
    $deptTI = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);
    $deptSI = Department::create(['faculty_id' => $faculty->id, 'name' => 'Sistem Informasi']);

    $kajurTI = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $deptTI->id,
        'name' => 'Dr. Kajur TI',
    ]);

    $kajurSI = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $deptSI->id,
        'name' => 'Dr. Kajur SI',
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $deptTI->id,
    ]);

    $mahasiswa = User::factory()->create([
        'role' => 'mahasiswa',
        'department_id' => $deptTI->id,
    ]);

    $courseTI = Course::create([
        'department_id' => $deptTI->id,
        'code'          => 'INF-101',
        'name'          => 'Dasar Pemrograman',
        'credit_hours'  => 3,
        'description'   => 'Mata kuliah dasar algoritma dan pemrograman.',
    ]);

    $courseSI = Course::create([
        'department_id' => $deptSI->id,
        'code'          => 'SI-101',
        'name'          => 'Pengantar Sistem Informasi',
        'credit_hours'  => 2,
        'description'   => 'Konsep dasar sistem informasi bisnis.',
    ]);

    return compact('faculty', 'deptTI', 'deptSI', 'kajurTI', 'kajurSI', 'dosen', 'mahasiswa', 'courseTI', 'courseSI');
}

/*
|--------------------------------------------------------------------------
| ACCESS CONTROL & AUTHORIZATION TESTS
|--------------------------------------------------------------------------
*/

test('unauthenticated users are redirected to login from kajur course routes', function () {
    $response = $this->get(route('kajur.courses.index'));
    $response->assertRedirect(route('login'));

    $response = $this->get(route('kajur.courses.create'));
    $response->assertRedirect(route('login'));
});

test('non-kajur users (dosen, mahasiswa) cannot access kajur course routes', function () {
    $s = createKajurCourseScenario();

    // Dosen attempt
    $this->actingAs($s['dosen'])
        ->get(route('kajur.courses.index'))
        ->assertStatus(403);

    $this->actingAs($s['dosen'])
        ->get(route('kajur.courses.create'))
        ->assertStatus(403);

    // Mahasiswa attempt
    $this->actingAs($s['mahasiswa'])
        ->get(route('kajur.courses.index'))
        ->assertStatus(403);
});

test('kajur without department assigned cannot access course routes', function () {
    $kajurNoDept = User::factory()->create([
        'role' => 'kajur',
        'department_id' => null,
    ]);

    $this->actingAs($kajurNoDept)
        ->get(route('kajur.courses.index'))
        ->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| INDEX & DEPARTMENT SCOPING TESTS
|--------------------------------------------------------------------------
*/

test('kajur can view list of courses strictly scoped to their own department', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.index'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Mata Kuliah (Kurikulum)');
    $response->assertSee('INF-101');
    $response->assertSee('Dasar Pemrograman');
    $response->assertDontSee('SI-101');
    $response->assertDontSee('Pengantar Sistem Informasi');
});

test('kajur can search courses by code and name within their department', function () {
    $s = createKajurCourseScenario();

    // Create another course in TI
    Course::create([
        'department_id' => $s['deptTI']->id,
        'code'          => 'INF-201',
        'name'          => 'Struktur Data Lanjut',
        'credit_hours'  => 3,
    ]);

    // Search by code
    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.index', ['search' => 'INF-201']));

    $response->assertStatus(200);
    $response->assertSee('INF-201');
    $response->assertSee('Struktur Data Lanjut');
    $response->assertDontSee('INF-101');

    // Search by name
    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.index', ['search' => 'Dasar Pemrograman']));

    $response->assertStatus(200);
    $response->assertSee('INF-101');
    $response->assertDontSee('INF-201');
});

/*
|--------------------------------------------------------------------------
| CREATE & STORE TESTS
|--------------------------------------------------------------------------
*/

test('kajur can view create course form', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.create'));

    $response->assertStatus(200);
    $response->assertSee('Tambah Mata Kuliah Baru');
    $response->assertSee('Teknik Informatika');
});

test('kajur can store a new course automatically assigned to their department', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->post(route('kajur.courses.store'), [
            'code'          => 'inf-302',
            'name'          => 'Rekayasa Perangkat Lunak',
            'credit_hours'  => 3,
            'description'   => 'Prinsip pengembangan perangkat lunak.',
        ]);

    $response->assertRedirect(route('kajur.courses.index'));
    $response->assertSessionHas('success');

    $course = Course::where('code', 'INF-302')->first();
    expect($course)->not->toBeNull()
        ->and($course->name)->toBe('Rekayasa Perangkat Lunak')
        ->and($course->department_id)->toBe($s['deptTI']->id)
        ->and($course->credit_hours)->toBe(3)
        ->and($course->description)->toBe('Prinsip pengembangan perangkat lunak.');
});

test('course creation validates required fields and uniqueness', function () {
    $s = createKajurCourseScenario();

    // Required validation
    $response = $this->actingAs($s['kajurTI'])
        ->post(route('kajur.courses.store'), [
            'code'          => '',
            'name'          => '',
            'credit_hours'  => '',
        ]);

    $response->assertSessionHasErrors(['code', 'name', 'credit_hours']);

    // Unique code validation
    $responseDuplicate = $this->actingAs($s['kajurTI'])
        ->post(route('kajur.courses.store'), [
            'code'          => 'INF-101', // Already exists in TI
            'name'          => 'Mata Kuliah Kloning',
            'credit_hours'  => 3,
        ]);

    $responseDuplicate->assertSessionHasErrors('code');
});

/*
|--------------------------------------------------------------------------
| EDIT & UPDATE TESTS
|--------------------------------------------------------------------------
*/

test('kajur can view edit page for a course in their own department', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.edit', $s['courseTI']));

    $response->assertStatus(200);
    $response->assertSee('Edit Mata Kuliah');
    $response->assertSee('INF-101');
    $response->assertSee('Dasar Pemrograman');
});

test('kajur cannot view or edit course belonging to another department', function () {
    $s = createKajurCourseScenario();

    // Kajur TI tries to edit course in SI
    $response = $this->actingAs($s['kajurTI'])
        ->get(route('kajur.courses.edit', $s['courseSI']));

    $response->assertStatus(403);

    // Kajur TI tries to update course in SI
    $responseUpdate = $this->actingAs($s['kajurTI'])
        ->put(route('kajur.courses.update', $s['courseSI']), [
            'code'         => 'SI-101-HACKED',
            'name'         => 'Hacked SI',
            'credit_hours' => 3,
        ]);

    $responseUpdate->assertStatus(403);
});

test('kajur can update course in their own department successfully', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->put(route('kajur.courses.update', $s['courseTI']), [
            'code'          => 'INF-101', // Keep same code (should not trigger unique error)
            'name'          => 'Dasar Pemrograman Komputer (Revisi)',
            'credit_hours'  => 4,
            'description'   => 'Deskripsi kurikulum yang telah diperbarui.',
        ]);

    $response->assertRedirect(route('kajur.courses.index'));
    $response->assertSessionHas('success');

    $s['courseTI']->refresh();
    expect($s['courseTI']->name)->toBe('Dasar Pemrograman Komputer (Revisi)')
        ->and($s['courseTI']->credit_hours)->toBe(4)
        ->and($s['courseTI']->description)->toBe('Deskripsi kurikulum yang telah diperbarui.')
        ->and($s['courseTI']->department_id)->toBe($s['deptTI']->id);
});

/*
|--------------------------------------------------------------------------
| DELETE TESTS
|--------------------------------------------------------------------------
*/

test('kajur cannot delete course belonging to another department', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->delete(route('kajur.courses.destroy', $s['courseSI']));

    $response->assertStatus(403);
    expect(Course::find($s['courseSI']->id))->not->toBeNull();
});

test('kajur can delete course belonging to their own department', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->delete(route('kajur.courses.destroy', $s['courseTI']));

    $response->assertRedirect(route('kajur.courses.index'));
    $response->assertSessionHas('success');

    // Soft delete check
    expect(Course::find($s['courseTI']->id))->toBeNull();
    expect(Course::withTrashed()->find($s['courseTI']->id))->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| NAVIGATION LINK TESTS
|--------------------------------------------------------------------------
*/

test('navigation bar displays Mata Kuliah link when authenticated as kajur', function () {
    $s = createKajurCourseScenario();

    $response = $this->actingAs($s['kajurTI'])
        ->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('kajur.courses.index'));
    $response->assertSee('Mata Kuliah');
});

