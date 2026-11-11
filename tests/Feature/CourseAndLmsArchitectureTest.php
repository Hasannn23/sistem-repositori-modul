<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\ModuleMeeting;
use App\Models\Sayembara;
use App\Models\User;

test('course can be created and has valid relations with department, classes, modules, and sayembaras', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Sains dan Teknologi']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Informatika']);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-201',
        'name'          => 'Struktur Data dan Algoritma',
        'credit_hours'  => 3,
        'description'   => 'Mata kuliah dasar struktur data dan algoritma.',
    ]);

    expect($course->exists)->toBeTrue()
        ->and($course->code)->toBe('INF-201')
        ->and($course->credit_hours)->toBe(3)
        ->and($course->department->id)->toBe($department->id);

    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    // Create CourseClass under Course
    $class = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas IF-A',
        'referral_code' => 'IFA2026',
        'academic_year' => '2026/2027',
        'semester'      => 'Ganjil',
    ]);

    // Create Module under Course & CourseClass
    $module = Module::create([
        'course_id'       => $course->id,
        'course_class_id' => $class->id,
        'title'           => 'Modul 1: Pengantar Linked List',
        'storage_type'    => 'pdf',
        'file_path'       => 'modules/linked_list.pdf',
        'is_locked'       => false,
        'status'          => 'approved',
    ]);

    // Create Sayembara under Course & Department
    $sayembara = Sayembara::create([
        'department_id' => $department->id,
        'course_id'     => $course->id,
        'title'         => 'Sayembara Modul Praktikum Struktur Data',
        'description'   => 'Sayembara penulisan modul praktikum berkualitas.',
        'start_date'    => '2026-11-01',
        'end_date'      => '2026-11-30',
        'status'        => 'open',
    ]);

    expect($course->courseClasses)->toHaveCount(1)
        ->and($course->courseClasses->first()->id)->toBe($class->id)
        ->and($class->course->id)->toBe($course->id)
        ->and($course->modules)->toHaveCount(1)
        ->and($course->modules->first()->id)->toBe($module->id)
        ->and($module->course->id)->toBe($course->id)
        ->and($course->sayembaras)->toHaveCount(1)
        ->and($course->sayembaras->first()->id)->toBe($sayembara->id)
        ->and($sayembara->course->id)->toBe($course->id)
        ->and($department->courses)->toHaveCount(1)
        ->and($department->sayembaras)->toHaveCount(1);
});

test('lms multi-pertemuan structure creates meetings and items correctly', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-101',
        'name'          => 'Pemrograman Dasar',
        'credit_hours'  => 3,
    ]);

    $class = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Pemrograman Dasar A',
        'referral_code' => 'PEMDAS_A',
        'academic_year' => '2026/2027',
        'semester'      => 'Ganjil',
    ]);

    $module = Module::create([
        'course_id'       => $course->id,
        'course_class_id' => $class->id,
        'title'           => 'Modul Praktikum Pemrograman Dasar',
        'storage_type'    => 'link',
        'file_path'       => 'https://example.com/modul-dasar',
        'is_locked'       => false,
        'status'          => 'approved',
    ]);

    // Buat Pertemuan 1
    $meeting1 = ModuleMeeting::create([
        'module_id'      => $module->id,
        'meeting_number' => 1,
        'title'          => 'Pertemuan 1: Pengenalan IDE dan Tipe Data',
        'description'    => 'Mahasiswa memahami dasar compiler dan variabel.',
    ]);

    // Item 1 (Text): Panduan Teori
    $itemText = ModuleItem::create([
        'module_meeting_id' => $meeting1->id,
        'title'             => 'Pengantar Teori & Sintaks',
        'type'              => 'text',
        'content_body'      => 'Penjelasan rinci mengenai variabel, tipe data primitif, dan operator aritmatika.',
        'order_index'       => 1,
    ]);

    // Item 2 (Video Link): Rekaman Praktik
    $itemVideo = ModuleItem::create([
        'module_meeting_id' => $meeting1->id,
        'title'             => 'Video Tutorial Setup Workspace',
        'type'              => 'video_link',
        'content_body'      => 'https://youtube.com/watch?v=sample123',
        'order_index'       => 2,
    ]);

    // Item 3 (File): Lembar Kerja PDF
    $itemFile = ModuleItem::create([
        'module_meeting_id' => $meeting1->id,
        'title'             => 'Lembar Kerja Praktikum 1',
        'type'              => 'file',
        'file_path'         => 'meetings/lk_pertemuan_1.pdf',
        'order_index'       => 3,
    ]);

    // Buat Pertemuan 2
    $meeting2 = ModuleMeeting::create([
        'module_id'      => $module->id,
        'meeting_number' => 2,
        'title'          => 'Pertemuan 2: Percabangan & Pengulangan',
        'description'    => 'Mahasiswa mempraktikkan If-Else dan Loop.',
    ]);

    // Assert Relasi Module -> Meetings -> Items
    $module->load('meetings.items');

    expect($module->meetings)->toHaveCount(2)
        ->and($module->meetings->first()->meeting_number)->toBe(1)
        ->and($module->meetings->first()->items)->toHaveCount(3)
        ->and($module->meetings->first()->items[0]->type)->toBe('text')
        ->and($module->meetings->first()->items[1]->type)->toBe('video_link')
        ->and($module->meetings->first()->items[2]->type)->toBe('file')
        ->and($itemText->meeting->id)->toBe($meeting1->id)
        ->and($meeting1->module->id)->toBe($module->id);
});

test('sayembara supports status open and closed', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Syariah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Hukum Ekonomi Syariah']);

    $sayembaraOpen = Sayembara::create([
        'department_id' => $department->id,
        'title'         => 'Sayembara Modul Fiqh Muamalah',
        'description'   => 'Terbuka untuk seluruh dosen pengampu bidang muamalah.',
        'brochure_path' => 'brochures/sayembara_muamalah.pdf',
        'start_date'    => '2026-11-01',
        'end_date'      => '2026-12-01',
        'status'        => 'open',
    ]);

    $sayembaraClosed = Sayembara::create([
        'department_id' => $department->id,
        'title'         => 'Sayembara Modul Pengantar Hukum Islam',
        'description'   => 'Periode penulisan telah berakhir.',
        'start_date'    => '2026-09-01',
        'end_date'      => '2026-10-01',
        'status'        => 'closed',
    ]);

    expect($sayembaraOpen->status)->toBe('open')
        ->and($sayembaraOpen->brochure_path)->toBe('brochures/sayembara_muamalah.pdf')
        ->and($sayembaraOpen->department->id)->toBe($department->id)
        ->and($sayembaraClosed->status)->toBe('closed');
});

