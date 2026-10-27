<?php

use App\Models\CourseClass;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('unauthenticated users cannot view classes or modules', function () {
    $this->get(route('classes.index'))->assertRedirect(route('login'));
    $this->get(route('modules.index'))->assertRedirect(route('login'));
});

test('dosen can view their classes', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $otherDosen = User::factory()->create(['role' => 'dosen']);

    $myClass = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Kelas Pemrograman Saya',
        'referral_code' => 'KODE01',
    ]);

    $otherClass = CourseClass::create([
        'dosen_id' => $otherDosen->id,
        'name' => 'Kelas Dosen Lain',
        'referral_code' => 'KODE02',
    ]);

    $response = $this->actingAs($dosen)->get(route('classes.index'));

    $response->assertStatus(200);
    $response->assertSee('Kelas Pemrograman Saya');
    $response->assertSee('KODE01');
    $response->assertDontSee('Kelas Dosen Lain');
});

test('dosen can store a new course class and auto-generate referral code', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);

    $response = $this->actingAs($dosen)->post(route('classes.store'), [
        'name' => 'Praktikum Basis Data',
    ]);

    $response->assertRedirect(route('classes.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_classes', [
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Basis Data',
    ]);

    $class = CourseClass::where('name', 'Praktikum Basis Data')->first();
    expect($class)->not->toBeNull();
    expect(strlen($class->referral_code))->toBe(6);
    expect($class->referral_code)->toBe(strtoupper($class->referral_code));
});

test('course class store requires name', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);

    $response = $this->actingAs($dosen)->post(route('classes.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('dosen can store a valid PDF module', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Jaringan Komputer',
        'referral_code' => 'NET123',
    ]);

    $file = UploadedFile::fake()->create('modul1.pdf', 1024, 'application/pdf'); // 1MB

    $response = $this->actingAs($dosen)->post(route('modules.store'), [
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Pengenalan IP Address',
        'file' => $file,
    ]);

    $response->assertRedirect(route('modules.index', ['class_id' => $class->id]));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('modules', [
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Pengenalan IP Address',
        'storage_type' => 'pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $module = Module::where('title', 'Modul 1: Pengenalan IP Address')->first();
    expect($module)->not->toBeNull();
    expect($module->is_locked)->toBeTrue();
    expect($module->status)->toBe('draft');
    expect($module->storage_type)->toBe('pdf');

    Storage::disk('public')->assertExists($module->file_path);
});

test('module store fails if file is not pdf', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Web',
        'referral_code' => 'WEB123',
    ]);

    $file = UploadedFile::fake()->create('modul.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $response = $this->actingAs($dosen)->post(route('modules.store'), [
        'course_class_id' => $class->id,
        'title' => 'Modul Word',
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
});

test('module store fails if file exceeds 5MB', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Web',
        'referral_code' => 'WEB123',
    ]);

    // 6MB = 6144 KB
    $file = UploadedFile::fake()->create('modul_besar.pdf', 6144, 'application/pdf');

    $response = $this->actingAs($dosen)->post(route('modules.store'), [
        'course_class_id' => $class->id,
        'title' => 'Modul Besar',
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
});

test('dosen can submit a draft module to kajur for review', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Cloud Computing',
        'referral_code' => 'CLD123',
    ]);
    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 1: Docker & Containerization',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modul1.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($dosen)->post(route('modules.submit', $module->id));

    $response->assertSessionHas('success');
    expect($module->fresh()->status)->toBe('waiting_review');
});

test('dosen can submit a revision module to kajur for review', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Cloud Computing',
        'referral_code' => 'CLD124',
    ]);
    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul 2: Kubernetes',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modul2.pdf',
        'is_locked' => true,
        'status' => 'revision',
        'kajur_notes' => 'Perbaiki bagian arsitektur pod.',
    ]);

    $response = $this->actingAs($dosen)->post(route('modules.submit', $module->id));

    $response->assertSessionHas('success');
    expect($module->fresh()->status)->toBe('waiting_review');
});

test('dosen cannot submit another dosens module', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $otherDosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $otherDosen->id,
        'name' => 'Praktikum Lain',
        'referral_code' => 'OTH123',
    ]);
    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Lain',
        'storage_type' => 'pdf',
        'file_path' => 'modules/modullain.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($dosen)->post(route('modules.submit', $module->id));

    $response->assertStatus(403);
    expect($module->fresh()->status)->toBe('draft');
});

test('module index displays status badges, submit button, and kajur notes', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Sistem Informasi',
        'referral_code' => 'SIS123',
    ]);

    $draftModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Draft Saya',
        'storage_type' => 'pdf',
        'file_path' => 'modules/draft.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $revisionModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Revisi Saya',
        'storage_type' => 'pdf',
        'file_path' => 'modules/revisi.pdf',
        'is_locked' => true,
        'status' => 'revision',
        'kajur_notes' => 'Tolong lengkapi bab 3 dengan studi kasus.',
    ]);

    $approvedModule = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Approved Saya',
        'storage_type' => 'pdf',
        'file_path' => 'modules/approved.pdf',
        'is_locked' => false,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($dosen)->get(route('modules.index', ['class_id' => $class->id]));

    $response->assertStatus(200);
    $response->assertSee('Draft');
    $response->assertSee('Perlu Revisi');
    $response->assertSee('Disetujui');
    $response->assertSee('Ajukan ke Kajur');
    $response->assertSee('Tolong lengkapi bab 3 dengan studi kasus.');
    $response->assertSee(route('modules.submit', $draftModule->id));
    $response->assertDontSee(route('modules.submit', $revisionModule->id));
    $response->assertSee(route('modules.updateRevision', $revisionModule->id));
    $response->assertSee('Upload Perbaikan');
    $response->assertDontSee(route('modules.submit', $approvedModule->id));
});

test('approved module hides kajur revision notes even if kajur_notes is set', function () {
    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Jaringan',
        'referral_code' => 'NET999',
    ]);

    Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Approved Dengan Catatan Lama',
        'storage_type' => 'pdf',
        'file_path' => 'modules/appr.pdf',
        'is_locked' => false,
        'status' => 'approved',
        'kajur_notes' => 'Catatan revisi lama yang seharusnya tidak tampil',
    ]);

    $response = $this->actingAs($dosen)->get(route('modules.index', ['class_id' => $class->id]));

    $response->assertStatus(200);
    $response->assertDontSee('Catatan revisi lama yang seharusnya tidak tampil');
});

test('dosen can upload a revision PDF module', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Sistem Informasi',
        'referral_code' => 'SIS124',
    ]);

    // Create old file in storage
    $oldFilePath = 'modules/modul_lama.pdf';
    Storage::disk('public')->put($oldFilePath, 'isi file lama');

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Rekayasa Web',
        'storage_type' => 'pdf',
        'file_path' => $oldFilePath,
        'is_locked' => true,
        'status' => 'revision',
        'kajur_notes' => 'Tolong revisi format daftar pustaka.',
    ]);

    $newFile = UploadedFile::fake()->create('modul_revisi.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), [
        'file' => $newFile,
    ]);

    $response->assertSessionHas('success');

    $module->refresh();
    expect($module->status)->toBe('waiting_review');
    expect($module->file_path)->not->toBe($oldFilePath);

    // Old file should be deleted from storage
    Storage::disk('public')->assertMissing($oldFilePath);

    // New file should exist in storage
    Storage::disk('public')->assertExists($module->file_path);
});

test('dosen cannot upload revision if module status is not revision', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Basis Data',
        'referral_code' => 'BDT123',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Draft',
        'storage_type' => 'pdf',
        'file_path' => 'modules/draft.pdf',
        'is_locked' => true,
        'status' => 'draft',
    ]);

    $file = UploadedFile::fake()->create('modul_revisi.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), [
        'file' => $file,
    ]);

    $response->assertSessionHas('error');
    expect($module->fresh()->status)->toBe('draft');
});

test('dosen cannot upload revision to another dosens module', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $otherDosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $otherDosen->id,
        'name' => 'Praktikum Lain',
        'referral_code' => 'OTH124',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Dosen Lain',
        'storage_type' => 'pdf',
        'file_path' => 'modules/other.pdf',
        'is_locked' => true,
        'status' => 'revision',
    ]);

    $file = UploadedFile::fake()->create('modul_revisi.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), [
        'file' => $file,
    ]);

    $response->assertStatus(403);
});

test('non-dosen cannot access updateRevision', function () {
    Storage::fake('public');

    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Dosen',
        'referral_code' => 'DOS123',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Revisi',
        'storage_type' => 'pdf',
        'file_path' => 'modules/revisi.pdf',
        'is_locked' => true,
        'status' => 'revision',
    ]);

    $file = UploadedFile::fake()->create('modul_revisi.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($mahasiswa)->post(route('modules.updateRevision', $module->id), [
        'file' => $file,
    ]);

    $response->assertStatus(403);
});

test('module updateRevision validates file is required, pdf, and max 5MB', function () {
    Storage::fake('public');

    $dosen = User::factory()->create(['role' => 'dosen']);
    $class = CourseClass::create([
        'dosen_id' => $dosen->id,
        'name' => 'Praktikum Web',
        'referral_code' => 'WEB124',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title' => 'Modul Web',
        'storage_type' => 'pdf',
        'file_path' => 'modules/web.pdf',
        'is_locked' => true,
        'status' => 'revision',
    ]);

    // Missing file
    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), []);
    $response->assertSessionHasErrors('file');

    // Not a PDF
    $docFile = UploadedFile::fake()->create('modul.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), ['file' => $docFile]);
    $response->assertSessionHasErrors('file');

    // Greater than 5MB (6MB)
    $largeFile = UploadedFile::fake()->create('modul_besar.pdf', 6144, 'application/pdf');
    $response = $this->actingAs($dosen)->post(route('modules.updateRevision', $module->id), ['file' => $largeFile]);
    $response->assertSessionHasErrors('file');
});

