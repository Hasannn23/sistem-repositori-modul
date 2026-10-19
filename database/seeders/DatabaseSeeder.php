<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat 1 Fakultas
        $faculty = Faculty::create([
            'name' => 'Fakultas Teknik',
        ]);

        // 2. Buat 1 Jurusan (Department) yang terikat ke Fakultas Teknik
        $department = Department::create([
            'faculty_id' => $faculty->id,
            'name' => 'Teknik Informatika',
        ]);

        // 3. Buat 4 Akun User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        $kajur = User::create([
            'department_id' => $department->id,
            'name' => 'Kajur TI',
            'email' => 'kajur@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'kajur',
        ]);

        $dosen = User::create([
            'department_id' => $department->id,
            'name' => 'Dosen TI',
            'email' => 'dosen@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $mahasiswa = User::create([
            'department_id' => $department->id,
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        // 4. Buat 1 Ruang Kelas (CourseClass)
        $courseClass = CourseClass::create([
            'dosen_id' => $dosen->id,
            'name' => 'Praktikum Pemrograman Web',
            'referral_code' => 'WEB2026',
        ]);

        // 5. Buat 2 Modul di dalam kelas tersebut
        // Modul 1: untuk tes tampil di halaman publik
        Module::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Modul 1: Pengenalan Laravel',
            'storage_type' => 'pdf',
            'file_path' => 'dummy.pdf',
            'status' => 'approved',
            'is_locked' => false,
            'kajur_id' => $kajur->id,
        ]);

        // Modul 2: untuk tes Dasbor Kajur
        Module::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Modul 2: Migration & Seeder',
            'storage_type' => 'pdf',
            'file_path' => 'dummy2.pdf',
            'status' => 'waiting_review',
            'is_locked' => true,
        ]);
    }
}
