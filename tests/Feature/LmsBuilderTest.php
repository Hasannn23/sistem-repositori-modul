<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\ModuleMeeting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Helper: Build a complete test scenario for LMS Builder
|--------------------------------------------------------------------------
*/
function createBuilderScenario(): array
{
    $faculty = Faculty::create(['name' => 'Fakultas Sains']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);
    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-301',
        'name'          => 'Basis Data Lanjut',
        'credit_hours'  => 3,
    ]);

    $dosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $department->id,
        'nidn' => '1234567890',
    ]);

    $otherDosen = User::factory()->create([
        'role' => 'dosen',
        'department_id' => $department->id,
    ]);

    $kajur = User::factory()->create([
        'role' => 'kajur',
        'department_id' => $department->id,
    ]);

    $courseClass = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas IF-A',
        'referral_code' => 'IFA3301',
        'academic_year' => '2026/2027',
        'semester'      => 'Ganjil',
    ]);

    $module = Module::create([
        'author_id'       => $dosen->id,
        'course_id'       => $course->id,
        'course_class_id' => $courseClass->id,
        'title'           => 'Modul Builder Test',
        'storage_type'    => 'lms',
        'status'          => 'draft',
        'is_locked'       => false,
    ]);

    return compact('faculty', 'department', 'course', 'dosen', 'otherDosen', 'kajur', 'courseClass', 'module');
}

/*
|--------------------------------------------------------------------------
| ACCESS CONTROL TESTS
|--------------------------------------------------------------------------
*/

test('unauthenticated user cannot access LMS builder', function () {
    $s = createBuilderScenario();
    $this->get(route('modules.builder', $s['module']))
        ->assertRedirect(route('login'));
});

test('author can access LMS builder for own draft module', function () {
    $s = createBuilderScenario();
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(200)
        ->assertSee('Kanvas Penyusunan Modul LMS');
});

test('other dosen cannot access LMS builder for module they do not own', function () {
    $s = createBuilderScenario();
    $this->actingAs($s['otherDosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(403);
});

test('locked module under review rejects builder access', function () {
    $s = createBuilderScenario();
    $s['module']->update(['is_locked' => true, 'status' => 'waiting_review']);

    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(403);
});

test('super_admin can access any module builder', function () {
    $s = createBuilderScenario();
    $admin = User::factory()->create(['role' => 'super_admin']);

    $this->actingAs($admin)
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(200);
});

test('dosen can access builder for module with revision status after unlock', function () {
    $s = createBuilderScenario();
    $s['module']->update(['status' => 'revision', 'is_locked' => false, 'kajur_notes' => 'Perbaiki materi.']);

    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| AUTOSAVE TESTS
|--------------------------------------------------------------------------
*/

test('autosave updates module title and returns JSON success', function () {
    $s = createBuilderScenario();

    $this->actingAs($s['dosen'])
        ->postJson(route('modules.builder.autosave', $s['module']), [
            'title' => 'Judul Baru Autosaved',
        ])
        ->assertOk()
        ->assertJson(['status' => 'success']);

    expect($s['module']->fresh()->title)->toBe('Judul Baru Autosaved');
});

test('autosave rejects empty title', function () {
    $s = createBuilderScenario();

    $this->actingAs($s['dosen'])
        ->postJson(route('modules.builder.autosave', $s['module']), [
            'title' => '',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('title');
});

/*
|--------------------------------------------------------------------------
| MEETING CRUD TESTS
|--------------------------------------------------------------------------
*/

test('dosen can create a new meeting in the builder', function () {
    $s = createBuilderScenario();

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.meetings.store', $s['module']))
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    $meeting = ModuleMeeting::where('module_id', $s['module']->id)->first();
    expect($meeting)->not->toBeNull()
        ->and($meeting->meeting_number)->toBe(1)
        ->and($meeting->title)->toBe('Pertemuan 1');
});

test('meeting numbers auto-increment correctly', function () {
    $s = createBuilderScenario();

    // Create 3 meetings
    for ($i = 0; $i < 3; $i++) {
        $this->actingAs($s['dosen'])
            ->post(route('modules.builder.meetings.store', $s['module']));
    }

    $meetings = ModuleMeeting::where('module_id', $s['module']->id)->orderBy('meeting_number')->get();
    expect($meetings)->toHaveCount(3)
        ->and($meetings[0]->meeting_number)->toBe(1)
        ->and($meetings[1]->meeting_number)->toBe(2)
        ->and($meetings[2]->meeting_number)->toBe(3);
});

test('dosen can update a meeting title and description', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $this->actingAs($s['dosen'])
        ->put(route('modules.builder.meetings.update', $meeting), [
            'title'       => 'Pertemuan 1: Entity Relationship',
            'description' => 'Membahas konsep ER diagram.',
        ])
        ->assertRedirect(route('modules.builder', $s['module']));

    $meeting->refresh();
    expect($meeting->title)->toBe('Pertemuan 1: Entity Relationship')
        ->and($meeting->description)->toBe('Membahas konsep ER diagram.');
});

test('dosen can delete a meeting and its items cascade', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'Item A',
        'type'              => 'text',
        'content_body'      => 'Konten...',
        'order_index'       => 1,
    ]);

    $this->actingAs($s['dosen'])
        ->delete(route('modules.builder.meetings.destroy', $meeting))
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    // SoftDeletes: cek trashed
    expect(ModuleMeeting::find($meeting->id))->toBeNull();
});

/*
|--------------------------------------------------------------------------
| ITEM CRUD TESTS
|--------------------------------------------------------------------------
*/

test('dosen can add a text item to a meeting', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.items.store', $meeting), [
            'type'         => 'text',
            'title'        => 'Konsep Normalisasi',
            'content_body' => 'Normalisasi adalah proses...',
        ])
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    $item = ModuleItem::where('module_meeting_id', $meeting->id)->first();
    expect($item)->not->toBeNull()
        ->and($item->type)->toBe('text')
        ->and($item->title)->toBe('Konsep Normalisasi')
        ->and($item->order_index)->toBe(1);
});

test('dosen can add a video_link item to a meeting', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.items.store', $meeting), [
            'type'         => 'video_link',
            'title'        => 'Video Pengantar SQL',
            'video_url'    => 'https://www.youtube.com/watch?v=abc123',
            'content_body' => 'Ringkasan video...',
        ])
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    $item = ModuleItem::where('module_meeting_id', $meeting->id)->first();
    expect($item)->not->toBeNull()
        ->and($item->type)->toBe('video_link')
        ->and($item->file_path)->toBe('https://www.youtube.com/watch?v=abc123');
});

test('dosen can add a file item to a meeting', function () {
    Storage::fake('public');
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $file = UploadedFile::fake()->create('materi.pdf', 500, 'application/pdf');

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.items.store', $meeting), [
            'type'  => 'file',
            'title' => 'Slide Kuliah PDF',
            'file'  => $file,
        ])
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    $item = ModuleItem::where('module_meeting_id', $meeting->id)->first();
    expect($item)->not->toBeNull()
        ->and($item->type)->toBe('file')
        ->and($item->file_path)->not->toBeNull();

    Storage::disk('public')->assertExists($item->file_path);
});

test('file upload rejects invalid mime types', function () {
    Storage::fake('public');
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $file = UploadedFile::fake()->create('malware.exe', 500);

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.items.store', $meeting), [
            'type'  => 'file',
            'title' => 'File Berbahaya',
            'file'  => $file,
        ])
        ->assertSessionHasErrors('file');
});

test('item order_index increments for multiple items', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    // Add 3 text items
    for ($i = 1; $i <= 3; $i++) {
        $this->actingAs($s['dosen'])
            ->post(route('modules.builder.items.store', $meeting), [
                'type'         => 'text',
                'title'        => "Materi {$i}",
                'content_body' => "Konten materi {$i}",
            ]);
    }

    $items = ModuleItem::where('module_meeting_id', $meeting->id)->orderBy('order_index')->get();
    expect($items)->toHaveCount(3)
        ->and($items[0]->order_index)->toBe(1)
        ->and($items[1]->order_index)->toBe(2)
        ->and($items[2]->order_index)->toBe(3);
});

test('dosen can delete an item', function () {
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $item = ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'Item to Delete',
        'type'              => 'text',
        'content_body'      => 'Content here',
        'order_index'       => 1,
    ]);

    $this->actingAs($s['dosen'])
        ->delete(route('modules.builder.items.destroy', $item))
        ->assertRedirect(route('modules.builder', $s['module']))
        ->assertSessionHas('success');

    expect(ModuleItem::find($item->id))->toBeNull();
});

test('deleting a file item also cleans up storage', function () {
    Storage::fake('public');
    $s = createBuilderScenario();
    $meeting = ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    Storage::disk('public')->put('module_items/test.pdf', 'dummy');

    $item = ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'PDF to Delete',
        'type'              => 'file',
        'file_path'         => 'module_items/test.pdf',
        'order_index'       => 1,
    ]);

    $this->actingAs($s['dosen'])
        ->delete(route('modules.builder.items.destroy', $item));

    Storage::disk('public')->assertMissing('module_items/test.pdf');
});

/*
|--------------------------------------------------------------------------
| SUBMIT MODULE TO KAJUR TESTS
|--------------------------------------------------------------------------
*/

test('dosen can submit module to kajur when meetings exist', function () {
    $s = createBuilderScenario();

    ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.submit', $s['module']))
        ->assertRedirect()
        ->assertSessionHas('success');

    $s['module']->refresh();
    expect($s['module']->status)->toBe('waiting_review')
        ->and($s['module']->is_locked)->toBeTrue();
});

test('submit is rejected when module has no meetings', function () {
    $s = createBuilderScenario();

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.submit', $s['module']))
        ->assertRedirect()
        ->assertSessionHas('error');

    $s['module']->refresh();
    expect($s['module']->status)->toBe('draft');
});

test('submitted module becomes locked and inaccessible in builder', function () {
    $s = createBuilderScenario();

    ModuleMeeting::create([
        'module_id'      => $s['module']->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1',
    ]);

    $this->actingAs($s['dosen'])
        ->post(route('modules.builder.submit', $s['module']));

    // Now try to access builder again — should be 403
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| KAJUR REVIEW FLOW — UNLOCK ON REVISION
|--------------------------------------------------------------------------
*/

test('kajur rejecting module unlocks it so dosen can edit in builder', function () {
    $s = createBuilderScenario();
    $s['module']->update(['status' => 'waiting_review', 'is_locked' => true]);

    $this->actingAs($s['kajur'])
        ->post(route('kajur.modules.reject', $s['module']), [
            'kajur_notes' => 'Tambahkan referensi di pertemuan 3.',
        ])
        ->assertRedirect();

    $s['module']->refresh();
    expect($s['module']->status)->toBe('revision')
        ->and($s['module']->is_locked)->toBeFalse()
        ->and($s['module']->kajur_notes)->toBe('Tambahkan referensi di pertemuan 3.');

    // Now dosen CAN access builder again
    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(200);
});

test('approved or master module rejects builder access for dosen', function () {
    $s = createBuilderScenario();
    $s['module']->update(['status' => 'approved', 'is_master' => true, 'is_locked' => false]);

    $this->actingAs($s['dosen'])
        ->get(route('modules.builder', $s['module']))
        ->assertStatus(403);
});


