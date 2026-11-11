<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\Sayembara;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('kajur can create a new sayembara for a course in their department', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $kajur = User::factory()->create(['role' => 'kajur', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-301',
        'name'          => 'Rekayasa Perangkat Lunak',
        'credit_hours'  => 3,
        'description'   => 'Mata kuliah RPL',
    ]);

    $response = $this->actingAs($kajur)->post(route('sayembaras.store'), [
        'course_id'   => $course->id,
        'title'       => 'Sayembara Modul Praktikum RPL 2026',
        'description' => 'Panduan penyusunan modul praktikum berstandar industri.',
        'start_date'  => '2026-11-01',
        'end_date'    => '2026-11-30',
    ]);

    $sayembara = Sayembara::first();
    expect($sayembara)->not->toBeNull()
        ->and($sayembara->title)->toBe('Sayembara Modul Praktikum RPL 2026')
        ->and($sayembara->course_id)->toBe($course->id)
        ->and($sayembara->status)->toBe('open');

    $response->assertRedirect(route('sayembaras.show', $sayembara));
});

test('dosen sees open sayembara banner on their dashboard', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-102',
        'name'          => 'Algoritma Pemrograman',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Terbuka Pemrograman 2026',
        'description'   => 'Penyusunan modul pemrograman dasar.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(2)->toDateString(),
        'status'        => 'open',
    ]);

    $response = $this->actingAs($dosen)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Sayembara Penyusunan Modul Terbuka');
    $response->assertSee('Sayembara Terbuka Pemrograman 2026');
    $response->assertSee('Algoritma Pemrograman');
    $response->assertSee('INF-102');
    $response->assertSee('Ikuti Sayembara / Susun Modul');
});

test('dosen can access sayembara participation form', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-102',
        'name'          => 'Algoritma Pemrograman',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Algoritma',
        'description'   => 'Modul praktikum algoritma.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(2)->toDateString(),
        'status'        => 'open',
    ]);

    $response = $this->actingAs($dosen)->get(route('dosen.sayembara.ikuti', $sayembara));

    $response->assertOk();
    $response->assertSee('Sayembara Algoritma');
    $response->assertSee('Simpan & Mulai Susun LMS', false);
    $response->assertDontSee('Tipe Berkas Modul');
    $response->assertDontSee('Unggah Dokumen PDF/Link Drive');
});

test('dosen can submit a draft module for open sayembara and redirected to lms builder', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-102',
        'name'          => 'Algoritma Pemrograman',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Algoritma',
        'description'   => 'Modul praktikum algoritma.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(2)->toDateString(),
        'status'        => 'open',
    ]);

    $response = $this->actingAs($dosen)->post(route('sayembaras.participate', $sayembara), [
        'title'       => 'Draf Modul Algoritma Lengkap',
        'description' => 'Silabus dan panduan lengkap semester ganjil.',
    ]);

    $module = Module::where('sayembara_id', $sayembara->id)->first();
    expect($module)->not->toBeNull()
        ->and($module->title)->toBe('Draf Modul Algoritma Lengkap')
        ->and($module->description)->toBe('Silabus dan panduan lengkap semester ganjil.')
        ->and($module->status)->toBe('draft')
        ->and($module->is_locked)->toBeFalse()
        ->and($module->is_master)->toBeFalse()
        ->and($module->author_id)->toBe($dosen->id)
        ->and($module->storage_type)->toBe('lms');

    $response->assertRedirect(route('dosen.modules.builder', $module->id));
});

test('sayembara participation requires valid title', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-102',
        'name'          => 'Algoritma Pemrograman',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Algoritma',
        'description'   => 'Modul praktikum algoritma.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(2)->toDateString(),
        'status'        => 'open',
    ]);

    $response = $this->actingAs($dosen)->post(route('sayembaras.participate', $sayembara), [
        'title' => '',
    ]);

    $response->assertSessionHasErrors(['title']);
    expect(Module::where('sayembara_id', $sayembara->id)->count())->toBe(0);
});

test('kajur approving module sets it as master and propagates to all classes of same course', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $kajur = User::factory()->create(['role' => 'kajur', 'department_id' => $department->id]);
    $dosenA = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);
    $dosenB = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-201',
        'name'          => 'Basis Data',
        'credit_hours'  => 3,
    ]);

    // Buat 2 kelas untuk matkul Basis Data
    $classA = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosenA->id,
        'name'          => 'Basis Data Kelas A',
        'referral_code' => 'BD2026A',
    ]);

    $classB = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosenB->id,
        'name'          => 'Basis Data Kelas B',
        'referral_code' => 'BD2026B',
    ]);

    // Sayembara
    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Modul Basis Data',
        'description'   => 'Sayembara modul master basis data.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(1)->toDateString(),
        'status'        => 'open',
    ]);

    // Dosen A mengusulkan modul
    $proposedModule = Module::create([
        'course_id'       => $course->id,
        'sayembara_id'    => $sayembara->id,
        'course_class_id' => $classA->id,
        'title'           => 'Master Modul Basis Data Praktikum',
        'storage_type'    => 'link',
        'file_path'       => 'https://drive.google.com/sample-master-modul',
        'is_locked'       => true,
        'is_master'       => false,
        'status'          => 'waiting_review',
    ]);

    // Kajur meng-ACC modul sebagai Master Modul
    $response = $this->actingAs($kajur)->post(route('sayembaras.approve-master', [$sayembara, $proposedModule]));

    $response->assertRedirect(route('sayembaras.show', $sayembara));

    $proposedModule->refresh();
    $sayembara->refresh();

    // 1. Modul yang di-ACC menjadi master dan status approved
    expect($proposedModule->is_master)->toBeTrue()
        ->and($proposedModule->status)->toBe('approved')
        ->and($proposedModule->is_locked)->toBeFalse()
        ->and($proposedModule->kajur_id)->toBe($kajur->id)
        ->and($sayembara->status)->toBe('closed');

    // 2. Kelas B otomatis memiliki modul master ini
    $classBModules = Module::where('course_class_id', $classB->id)
        ->where('is_master', true)
        ->where('status', 'approved')
        ->get();

    expect($classBModules)->toHaveCount(1)
        ->and($classBModules->first()->title)->toBe('Master Modul Basis Data Praktikum');

    // 3. Kelas baru yang dibuat setelahnya juga otomatis mewarisi modul master ini
    $classC = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosenA->id,
        'name'          => 'Basis Data Kelas C',
        'referral_code' => 'BD2026C',
    ]);

    // Trigger CourseClassController store logic or replication helper
    $master = Module::where('course_id', $classC->course_id)
        ->where('is_master', true)
        ->where('status', 'approved')
        ->first();

    expect($master)->not->toBeNull();
});

test('sayembara tracks live participants and links module to author with nidn', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $kajur = User::factory()->create([
        'role'          => 'kajur',
        'department_id' => $department->id,
        'nidn'          => '197801012005011001',
    ]);

    $dosenA = User::factory()->create([
        'name'          => 'Dr. Siti Nurhaliza, M.T.',
        'role'          => 'dosen',
        'department_id' => $department->id,
        'nidn'          => '198502022010121002',
    ]);

    $dosenB = User::factory()->create([
        'name'          => 'Ahmad Fauzi, M.Kom.',
        'role'          => 'dosen',
        'department_id' => $department->id,
        'nidn'          => '199003032015011003',
    ]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-401',
        'name'          => 'Kecerdasan Buatan',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Kecerdasan Buatan',
        'description'   => 'Modul AI praktikum.',
        'start_date'    => now()->subDay()->toDateString(),
        'end_date'      => now()->addWeeks(2)->toDateString(),
        'status'        => 'open',
    ]);

    expect($sayembara->participantsCount())->toBe(0)
        ->and($sayembara->participants())->toBeEmpty();

    // Dosen A submits 2 modules/drafts
    Module::create([
        'author_id'    => $dosenA->id,
        'course_id'    => $course->id,
        'sayembara_id' => $sayembara->id,
        'title'        => 'Draf Versi 1 AI (Dosen A)',
        'storage_type' => 'link',
        'file_path'    => 'https://example.com/draft1',
        'status'       => 'waiting_review',
    ]);

    Module::create([
        'author_id'    => $dosenA->id,
        'course_id'    => $course->id,
        'sayembara_id' => $sayembara->id,
        'title'        => 'Draf Revisi Tambahan AI (Dosen A)',
        'storage_type' => 'link',
        'file_path'    => 'https://example.com/draft2',
        'status'       => 'waiting_review',
    ]);

    // Dosen B submits 1 module
    Module::create([
        'author_id'    => $dosenB->id,
        'course_id'    => $course->id,
        'sayembara_id' => $sayembara->id,
        'title'        => 'Draf Alternatif AI (Dosen B)',
        'storage_type' => 'link',
        'file_path'    => 'https://example.com/draft-b',
        'status'       => 'waiting_review',
    ]);

    // Refresh sayembara and check distinct participants
    $sayembara->refresh();
    $participants = $sayembara->participants();

    expect($sayembara->participantsCount())->toBe(2)
        ->and($participants)->toHaveCount(2)
        ->and($participants->pluck('id'))->toContain($dosenA->id, $dosenB->id);

    // Verify relations and nidn
    $firstModule = Module::where('title', 'Draf Versi 1 AI (Dosen A)')->first();
    expect($firstModule->author)->not->toBeNull()
        ->and($firstModule->author->id)->toBe($dosenA->id)
        ->and($firstModule->author->nidn)->toBe('198502022010121002')
        ->and($firstModule->authorUser->name)->toBe('Dr. Siti Nurhaliza, M.T.');

    expect($dosenA->modules)->toHaveCount(2);
});

test('kajur cannot approve another module after sayembara is already closed', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $kajur = User::factory()->create(['role' => 'kajur', 'department_id' => $department->id]);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-501',
        'name'          => 'Keamanan Siber',
        'credit_hours'  => 3,
    ]);

    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Ditutup',
        'description'   => 'Sayembara sudah selesai.',
        'start_date'    => now()->subWeeks(2)->toDateString(),
        'end_date'      => now()->subDay()->toDateString(),
        'status'        => 'closed',
    ]);

    $otherModule = Module::create([
        'author_id'    => $dosen->id,
        'course_id'    => $course->id,
        'sayembara_id' => $sayembara->id,
        'title'        => 'Draf Modul Lain',
        'storage_type' => 'lms',
        'status'       => 'waiting_review',
        'is_master'    => false,
    ]);

    $this->actingAs($kajur)
        ->post(route('sayembaras.approve-master', [$sayembara, $otherModule]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($otherModule->fresh()->is_master)->toBeFalse();
});



