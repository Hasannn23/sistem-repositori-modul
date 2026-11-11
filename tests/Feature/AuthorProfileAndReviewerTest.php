<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\ModuleMeeting;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Helper: Build a reviewer test scenario
|--------------------------------------------------------------------------
*/
function createReviewerScenario(): array
{
    $faculty = Faculty::create(['name' => 'Fakultas Sains']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);
    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-401',
        'name'          => 'Kecerdasan Buatan',
        'credit_hours'  => 3,
    ]);

    $dosen = User::factory()->create([
        'role'          => 'dosen',
        'department_id' => $department->id,
        'nidn'          => '9876543210',
    ]);

    $kajur = User::factory()->create([
        'role'          => 'kajur',
        'department_id' => $department->id,
    ]);

    $courseClass = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas IF-B',
        'referral_code' => 'IFB4401',
        'academic_year' => '2026/2027',
        'semester'      => 'Ganjil',
    ]);

    $module = Module::create([
        'author_id'       => $dosen->id,
        'course_id'       => $course->id,
        'course_class_id' => $courseClass->id,
        'title'           => 'Modul Kecerdasan Buatan',
        'storage_type'    => 'lms',
        'status'          => 'waiting_review',
        'is_locked'       => true,
    ]);

    // Create meetings so LMS reader has content
    $meeting = ModuleMeeting::create([
        'module_id'      => $module->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1: Intro AI',
    ]);

    return compact('faculty', 'department', 'course', 'dosen', 'kajur', 'courseClass', 'module', 'meeting');
}

/*
|--------------------------------------------------------------------------
| AUTHOR PROFILE CARD RENDERING TESTS
|--------------------------------------------------------------------------
*/

test('kajur viewing waiting_review module in LMS reader sees author profile card with ACC and Revisi buttons', function () {
    $s = createReviewerScenario();

    $response = $this->actingAs($s['kajur'])
        ->get(route('modules.lms', $s['module']));

    $response->assertStatus(200)
        ->assertSee($s['dosen']->name)
        ->assertSee('9876543210') // NIDN
        ->assertSee('ACC / Terima Modul')
        ->assertSee('Kembalikan');
});

test('dosen viewing own waiting_review module does NOT see ACC/Revisi buttons', function () {
    $s = createReviewerScenario();

    $response = $this->actingAs($s['dosen'])
        ->get(route('modules.lms', $s['module']));

    $response->assertStatus(200)
        ->assertDontSee('ACC / Terima Modul')
        ->assertDontSee('Kembalikan (Revisi)');
});

test('author profile card does NOT render for draft modules even for kajur', function () {
    $s = createReviewerScenario();
    $s['module']->update(['status' => 'draft', 'is_locked' => false]);

    $response = $this->actingAs($s['kajur'])
        ->get(route('modules.lms', $s['module']));

    $response->assertStatus(200)
        ->assertDontSee('ACC / Terima Modul');
});

test('author profile card shows revision notes for module with revision status', function () {
    $s = createReviewerScenario();
    $s['module']->update([
        'status'      => 'revision',
        'is_locked'   => false,
        'kajur_notes' => 'Tambahkan referensi jurnal.',
    ]);

    $response = $this->actingAs($s['kajur'])
        ->get(route('modules.lms', $s['module']));

    $response->assertStatus(200)
        ->assertSee('Tambahkan referensi jurnal.');
});

/*
|--------------------------------------------------------------------------
| INLINE KAJUR REVIEW ACTION TESTS
|--------------------------------------------------------------------------
*/

test('kajur can approve module from inline review and it becomes accessible to students', function () {
    $s = createReviewerScenario();

    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.approve', $s['module']))
        ->assertRedirect()
        ->assertSessionHas('success');

    $s['module']->refresh();
    expect($s['module']->status)->toBe('approved')
        ->and($s['module']->is_locked)->toBeFalse()
        ->and($s['module']->kajur_id)->toBe($s['kajur']->id);

    // Approved module is accessible by any authenticated user
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $this->actingAs($student)
        ->get(route('modules.lms', $s['module']))
        ->assertStatus(200);
});

test('kajur can reject module with revision notes from inline review', function () {
    $s = createReviewerScenario();

    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.reject', $s['module']), [
            'kajur_notes' => 'Perbaiki format dan tambahkan gambar.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $s['module']->refresh();
    expect($s['module']->status)->toBe('revision')
        ->and($s['module']->is_locked)->toBeFalse()
        ->and($s['module']->kajur_notes)->toBe('Perbaiki format dan tambahkan gambar.');
});

test('kajur cannot reject without providing notes', function () {
    $s = createReviewerScenario();

    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.reject', $s['module']), [
            'kajur_notes' => '',
        ])
        ->assertSessionHasErrors('kajur_notes');
});

test('kajur from different department cannot approve module', function () {
    $s = createReviewerScenario();

    $otherDept = Department::create(['faculty_id' => $s['faculty']->id, 'name' => 'Sistem Informasi']);
    $otherKajur = User::factory()->create([
        'role'          => 'kajur',
        'department_id' => $otherDept->id,
    ]);

    $this->actingAs($otherKajur)
        ->post(route('kajur.modules.approve', $s['module']))
        ->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| FULL LIFECYCLE: BUILDER → SUBMIT → REVIEW → REVISION → RESUBMIT
|--------------------------------------------------------------------------
*/

test('complete lifecycle: draft → submit → revision → resubmit → approved', function () {
    $s = createReviewerScenario();
    $module = $s['module'];

    // 1. Reset to draft state
    $module->update(['status' => 'draft', 'is_locked' => false]);

    // 2. Access builder
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $module))
        ->assertStatus(200);

    // 3. Submit to kajur
    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.submit', $module));

    $module->refresh();
    expect($module->status)->toBe('waiting_review')
        ->and($module->is_locked)->toBeTrue();

    // 4. Builder blocked
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $module))
        ->assertStatus(403);

    // 5. Kajur rejects
    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.reject', $module), [
            'kajur_notes' => 'Kurang detail di pertemuan 1.',
        ]);

    $module->refresh();
    expect($module->status)->toBe('revision')
        ->and($module->is_locked)->toBeFalse();

    // 6. Builder accessible again for revision
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $module))
        ->assertStatus(200);

    // 7. Re-submit to kajur
    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.submit', $module));

    $module->refresh();
    expect($module->status)->toBe('waiting_review')
        ->and($module->is_locked)->toBeTrue();

    // 8. Kajur approves
    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.approve', $module));

    $module->refresh();
    expect($module->status)->toBe('approved')
        ->and($module->is_locked)->toBeFalse()
        ->and($module->kajur_id)->toBe($s['kajur']->id);
});

