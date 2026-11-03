<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CampusDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campuses = [
            'Fakultas Ilmu Tarbiyah dan Keguruan' => [
                'Informatika',
                'Manajemen Pendidikan Islam',
                'Matematika',
                'Pendidikan Agama Islam',
            ],
            'Fakultas Syariah' => [
                'Hukum Ekonomi Syariah',
                'Hukum Tatanegara',
                'Hukum Keluarga',
            ],
            'Fakultas Ekonomi dan Bisnis Islam' => [
                'Akuntansi Syariah',
                'Bioteknologi',
                'Ekonomi Syariah',
            ],
        ];

        $departments = [];

        foreach ($campuses as $facultyName => $departmentNames) {
            $faculty = Faculty::firstOrCreate([
                'name' => $facultyName,
            ]);

            foreach ($departmentNames as $departmentName) {
                $departments[$departmentName] = Department::firstOrCreate([
                    'faculty_id' => $faculty->id,
                    'name' => $departmentName,
                ]);
            }
        }

        $dummyUsers = [
            [
                'email' => 'kajur.info@kampus.ac.id',
                'name' => 'Kajur Informatika',
                'role' => 'kajur',
                'department_name' => 'Informatika',
            ],
            [
                'email' => 'dosen.info@kampus.ac.id',
                'name' => 'Dosen Informatika',
                'role' => 'dosen',
                'department_name' => 'Informatika',
            ],
            [
                'email' => 'kajur.hes@kampus.ac.id',
                'name' => 'Kajur Hukum Ekonomi Syariah',
                'role' => 'kajur',
                'department_name' => 'Hukum Ekonomi Syariah',
            ],
            [
                'email' => 'dosen.hes@kampus.ac.id',
                'name' => 'Dosen Hukum Ekonomi Syariah',
                'role' => 'dosen',
                'department_name' => 'Hukum Ekonomi Syariah',
            ],
        ];

        foreach ($dummyUsers as $user) {
            $department = $departments[$user['department_name']]
                ?? Department::where('name', $user['department_name'])->first();

            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'department_id' => $department?->id,
                ]
            );
        }
    }
}

