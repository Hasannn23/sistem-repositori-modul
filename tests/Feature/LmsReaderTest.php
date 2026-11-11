<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\ModuleMeeting;
use App\Models\User;

test('authenticated user can view lms reader with meetings and items', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);
    $student = User::factory()->create(['role' => 'mahasiswa', 'department_id' => $department->id]);

    $course = Course::create([
        'department_id' => $department->id,
        'code'          => 'INF-105',
        'name'          => 'Pemrograman Web Modern',
        'credit_hours'  => 3,
    ]);

    $class = CourseClass::create([
        'course_id'     => $course->id,
        'dosen_id'      => $dosen->id,
        'name'          => 'Web A',
        'referral_code' => 'WEBA26',
    ]);

    $class->students()->attach($student->id);

    $module = Module::create([
        'course_id'       => $course->id,
        'course_class_id' => $class->id,
        'title'           => 'Modul Praktikum Laravel & Alpine',
        'storage_type'    => 'link',
        'file_path'       => 'https://example.com/modul-web',
        'is_locked'       => false,
        'status'          => 'approved',
    ]);

    $meeting = ModuleMeeting::create([
        'module_id'      => $module->id,
        'meeting_number' => 1,
        'title'          => 'Instalasi & Pengenalan Blade',
        'description'    => 'Panduan awal belajar Blade engine.',
    ]);

    ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'Pengantar Sintaks Blade',
        'type'              => 'text',
        'content_body'      => 'Blade adalah templating engine sederhana namun kuat bawaan Laravel.',
        'order_index'       => 1,
    ]);

    ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'Video Tutorial Setup Workspace',
        'type'              => 'video_link',
        'content_body'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'order_index'       => 2,
    ]);

    ModuleItem::create([
        'module_meeting_id' => $meeting->id,
        'title'             => 'Lembar Kerja Mandiri 1',
        'type'              => 'file',
        'file_path'         => 'meetings/lk_1.pdf',
        'order_index'       => 3,
    ]);

    $response = $this->actingAs($student)->get(route('modules.lms', $module));

    $response->assertStatus(200);
    $response->assertSee('Modul Praktikum Laravel & Alpine');
    $response->assertSee('Pemrograman Web Modern');
    $response->assertSee('Pertemuan 1');
    $response->assertSee('Instalasi & Pengenalan Blade');
    $response->assertSee('Pengantar Sintaks Blade');
    $response->assertSee('Video Tutorial Setup Workspace');
    $response->assertSee('Lembar Kerja Mandiri 1');
});

test('unauthorized guest cannot access lms reader', function () {
    $faculty = Faculty::create(['name' => 'Fakultas Tarbiyah']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);
    $dosen = User::factory()->create(['role' => 'dosen', 'department_id' => $department->id]);

    $class = CourseClass::create([
        'dosen_id'      => $dosen->id,
        'name'          => 'Kelas Draft',
        'referral_code' => 'DRAFT1',
    ]);

    $module = Module::create([
        'course_class_id' => $class->id,
        'title'           => 'Modul Rahasia',
        'storage_type'    => 'pdf',
        'file_path'       => 'modules/rahasia.pdf',
        'is_locked'       => true,
        'status'          => 'draft',
    ]);

    // Guest redirected to login
    $response = $this->get(route('modules.lms', $module));
    $response->assertRedirect(route('login'));

    // Non-owner student forbidden on draft module
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $studentResponse = $this->actingAs($student)->get(route('modules.lms', $module));
    $studentResponse->assertStatus(403);
});

