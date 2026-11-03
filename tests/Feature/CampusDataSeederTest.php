<?php

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Database\Seeders\CampusDataSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

test('campus data seeder creates faculties, departments, and dummy users correctly', function () {
    $this->seed(CampusDataSeeder::class);

    // 1. Verify Faculties
    $fitk = Faculty::where('name', 'Fakultas Ilmu Tarbiyah dan Keguruan')->first();
    $syariah = Faculty::where('name', 'Fakultas Syariah')->first();
    $febi = Faculty::where('name', 'Fakultas Ekonomi dan Bisnis Islam')->first();

    expect($fitk)->not->toBeNull()
        ->and($syariah)->not->toBeNull()
        ->and($febi)->not->toBeNull();

    // 2. Verify Departments
    $fitkDepts = ['Informatika', 'Manajemen Pendidikan Islam', 'Matematika', 'Pendidikan Agama Islam'];
    foreach ($fitkDepts as $deptName) {
        $dept = Department::where('name', $deptName)->where('faculty_id', $fitk->id)->first();
        expect($dept)->not->toBeNull();
    }

    $syariahDepts = ['Hukum Ekonomi Syariah', 'Hukum Tatanegara', 'Hukum Keluarga'];
    foreach ($syariahDepts as $deptName) {
        $dept = Department::where('name', $deptName)->where('faculty_id', $syariah->id)->first();
        expect($dept)->not->toBeNull();
    }

    $febiDepts = ['Akuntansi Syariah', 'Bioteknologi', 'Ekonomi Syariah'];
    foreach ($febiDepts as $deptName) {
        $dept = Department::where('name', $deptName)->where('faculty_id', $febi->id)->first();
        expect($dept)->not->toBeNull();
    }

    // 3. Verify Dummy Users
    $infoDept = Department::where('name', 'Informatika')->first();
    $hesDept = Department::where('name', 'Hukum Ekonomi Syariah')->first();

    $kajurInfo = User::where('email', 'kajur.info@kampus.ac.id')->first();
    expect($kajurInfo)->not->toBeNull()
        ->and($kajurInfo->role)->toBe('kajur')
        ->and($kajurInfo->department_id)->toBe($infoDept->id)
        ->and(Hash::check('password', $kajurInfo->password))->toBeTrue();

    $dosenInfo = User::where('email', 'dosen.info@kampus.ac.id')->first();
    expect($dosenInfo)->not->toBeNull()
        ->and($dosenInfo->role)->toBe('dosen')
        ->and($dosenInfo->department_id)->toBe($infoDept->id)
        ->and(Hash::check('password', $dosenInfo->password))->toBeTrue();

    $kajurHes = User::where('email', 'kajur.hes@kampus.ac.id')->first();
    expect($kajurHes)->not->toBeNull()
        ->and($kajurHes->role)->toBe('kajur')
        ->and($kajurHes->department_id)->toBe($hesDept->id)
        ->and(Hash::check('password', $kajurHes->password))->toBeTrue();

    $dosenHes = User::where('email', 'dosen.hes@kampus.ac.id')->first();
    expect($dosenHes)->not->toBeNull()
        ->and($dosenHes->role)->toBe('dosen')
        ->and($dosenHes->department_id)->toBe($hesDept->id)
        ->and(Hash::check('password', $dosenHes->password))->toBeTrue();
});

test('campus data seeder is idempotent and can be run multiple times safely', function () {
    $this->seed(CampusDataSeeder::class);
    $initialFaculties = Faculty::count();
    $initialDepartments = Department::count();
    $initialUsers = User::count();

    // Run again
    $this->seed(CampusDataSeeder::class);

    expect(Faculty::count())->toBe($initialFaculties)
        ->and(Department::count())->toBe($initialDepartments)
        ->and(User::count())->toBe($initialUsers);
});

test('database seeder includes campus data seeder', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Faculty::where('name', 'Fakultas Ilmu Tarbiyah dan Keguruan')->exists())->toBeTrue()
        ->and(User::where('email', 'kajur.info@kampus.ac.id')->exists())->toBeTrue();
});

