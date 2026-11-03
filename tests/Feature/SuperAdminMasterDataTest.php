<?php

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('non-super_admin users cannot access master data routes', function () {
    $guest = null;
    $student = User::factory()->create(['role' => 'mahasiswa']);
    $dosen = User::factory()->create(['role' => 'dosen']);
    $kajur = User::factory()->create(['role' => 'kajur']);

    // Guests are redirected to login
    $this->get(route('faculties.index'))->assertRedirect(route('login'));
    $this->get(route('departments.index'))->assertRedirect(route('login'));
    $this->get(route('users.index'))->assertRedirect(route('login'));

    // Non-superadmin roles are forbidden (403)
    $this->actingAs($student)->get(route('faculties.index'))->assertStatus(403);
    $this->actingAs($dosen)->get(route('departments.index'))->assertStatus(403);
    $this->actingAs($kajur)->get(route('users.index'))->assertStatus(403);
});

test('super admin can view master data navigation links', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($superAdmin)->get(route('faculties.index'));

    $response->assertStatus(200);
    $response->assertSee('Kelola Fakultas');
    $response->assertSee('Kelola Jurusan');
    $response->assertSee('Kelola Pengguna');
});

test('super admin can perform full CRUD on faculties', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    // 1. Index
    $response = $this->actingAs($superAdmin)->get(route('faculties.index'));
    $response->assertStatus(200);
    $response->assertSee('Kelola Fakultas');

    // 2. Create form
    $createResponse = $this->actingAs($superAdmin)->get(route('faculties.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Tambah Fakultas Baru');

    // 3. Store
    $storeResponse = $this->actingAs($superAdmin)->post(route('faculties.store'), [
        'name' => 'Fakultas Ilmu Komputer',
    ]);
    $storeResponse->assertRedirect(route('faculties.index'));
    $storeResponse->assertSessionHas('success');
    $this->assertDatabaseHas('faculties', ['name' => 'Fakultas Ilmu Komputer']);

    $faculty = Faculty::where('name', 'Fakultas Ilmu Komputer')->first();

    // 4. Edit form
    $editResponse = $this->actingAs($superAdmin)->get(route('faculties.edit', $faculty));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Edit Fakultas');
    $editResponse->assertSee('Fakultas Ilmu Komputer');

    // 5. Update
    $updateResponse = $this->actingAs($superAdmin)->put(route('faculties.update', $faculty), [
        'name' => 'Fakultas Teknologi Informasi',
    ]);
    $updateResponse->assertRedirect(route('faculties.index'));
    $updateResponse->assertSessionHas('success');
    $this->assertDatabaseHas('faculties', ['id' => $faculty->id, 'name' => 'Fakultas Teknologi Informasi']);

    // 6. Destroy
    $deleteResponse = $this->actingAs($superAdmin)->delete(route('faculties.destroy', $faculty));
    $deleteResponse->assertRedirect(route('faculties.index'));
    $deleteResponse->assertSessionHas('success');
    $this->assertSoftDeleted('faculties', ['id' => $faculty->id]);
});

test('super admin can perform full CRUD on departments with faculty relation', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $faculty = Faculty::create(['name' => 'Fakultas Sains dan Teknologi']);
    $faculty2 = Faculty::create(['name' => 'Fakultas Ekonomi dan Bisnis']);

    // 1. Index
    $response = $this->actingAs($superAdmin)->get(route('departments.index'));
    $response->assertStatus(200);
    $response->assertSee('Kelola Jurusan');

    // 2. Create form contains faculty dropdown
    $createResponse = $this->actingAs($superAdmin)->get(route('departments.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Fakultas Sains dan Teknologi');
    $createResponse->assertSee('Fakultas Ekonomi dan Bisnis');

    // 3. Store
    $storeResponse = $this->actingAs($superAdmin)->post(route('departments.store'), [
        'faculty_id' => $faculty->id,
        'name' => 'Teknik Elektro',
    ]);
    $storeResponse->assertRedirect(route('departments.index'));
    $storeResponse->assertSessionHas('success');
    $this->assertDatabaseHas('departments', [
        'faculty_id' => $faculty->id,
        'name' => 'Teknik Elektro',
    ]);

    $department = Department::where('name', 'Teknik Elektro')->first();

    // 4. Edit form
    $editResponse = $this->actingAs($superAdmin)->get(route('departments.edit', $department));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Teknik Elektro');

    // 5. Update
    $updateResponse = $this->actingAs($superAdmin)->put(route('departments.update', $department), [
        'faculty_id' => $faculty2->id,
        'name' => 'Sistem Informasi Bisnis',
    ]);
    $updateResponse->assertRedirect(route('departments.index'));
    $updateResponse->assertSessionHas('success');
    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'faculty_id' => $faculty2->id,
        'name' => 'Sistem Informasi Bisnis',
    ]);

    // 6. Destroy
    $deleteResponse = $this->actingAs($superAdmin)->delete(route('departments.destroy', $department));
    $deleteResponse->assertRedirect(route('departments.index'));
    $deleteResponse->assertSessionHas('success');
    $this->assertSoftDeleted('departments', ['id' => $department->id]);
});

test('super admin can perform full CRUD on users with default password', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Informatika']);

    // 1. Index
    $response = $this->actingAs($superAdmin)->get(route('users.index'));
    $response->assertStatus(200);
    $response->assertSee('Kelola Pengguna');

    // 2. Create form has role dropdown and department dropdown
    $createResponse = $this->actingAs($superAdmin)->get(route('users.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Informatika');
    $createResponse->assertSee('Dosen');
    $createResponse->assertSee('Kepala Jurusan');

    // 3. Store with default password
    $storeResponse = $this->actingAs($superAdmin)->post(route('users.store'), [
        'name' => 'Dosen Baru Test',
        'email' => 'dosen.baru@test.com',
        'role' => 'dosen',
        'department_id' => $department->id,
    ]);
    $storeResponse->assertRedirect(route('users.index'));
    $storeResponse->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'Dosen Baru Test',
        'email' => 'dosen.baru@test.com',
        'role' => 'dosen',
        'department_id' => $department->id,
    ]);

    $createdUser = User::where('email', 'dosen.baru@test.com')->first();
    expect($createdUser)->not->toBeNull();
    // Default password must be 'password'
    expect(Hash::check('password', $createdUser->password))->toBeTrue();

    // 4. Edit form
    $editResponse = $this->actingAs($superAdmin)->get(route('users.edit', $createdUser));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Dosen Baru Test');

    // 5. Update
    $updateResponse = $this->actingAs($superAdmin)->put(route('users.update', $createdUser), [
        'name' => 'Dosen Terupdate',
        'email' => 'dosen.update@test.com',
        'role' => 'kajur',
        'department_id' => $department->id,
    ]);
    $updateResponse->assertRedirect(route('users.index'));
    $updateResponse->assertSessionHas('success');
    $this->assertDatabaseHas('users', [
        'id' => $createdUser->id,
        'name' => 'Dosen Terupdate',
        'email' => 'dosen.update@test.com',
        'role' => 'kajur',
    ]);

    // 6. Super Admin cannot delete own account
    $selfDeleteResponse = $this->actingAs($superAdmin)->delete(route('users.destroy', $superAdmin));
    $selfDeleteResponse->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'deleted_at' => null]);

    // 7. Delete other user
    $deleteResponse = $this->actingAs($superAdmin)->delete(route('users.destroy', $createdUser));
    $deleteResponse->assertRedirect(route('users.index'));
    $deleteResponse->assertSessionHas('success');
    $this->assertSoftDeleted('users', ['id' => $createdUser->id]);
});

test('super admin can search departments by name', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $faculty = Faculty::create(['name' => 'Fakultas Teknik']);
    Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Sipil']);
    Department::create(['faculty_id' => $faculty->id, 'name' => 'Teknik Elektro']);

    $response = $this->actingAs($superAdmin)->get(route('departments.index', ['search' => 'Sipil']));
    $response->assertStatus(200);
    $response->assertSee('Teknik Sipil');
    $response->assertDontSee('Teknik Elektro');
});

test('super admin can search users by name', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    User::factory()->create(['name' => 'Ahmad Dahlan', 'email' => 'ahmad@test.com']);
    User::factory()->create(['name' => 'Budi Utomo', 'email' => 'budi@test.com']);

    $response = $this->actingAs($superAdmin)->get(route('users.index', ['search' => 'Ahmad']));
    $response->assertStatus(200);
    $response->assertSee('Ahmad Dahlan');
    $response->assertDontSee('Budi Utomo');
});
