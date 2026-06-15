<?php

namespace Tests\Feature\Auth;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // TC-AUTH-001
    public function test_student_can_register_with_valid_data(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->postJson('/api/auth/register', [
            'username'            => 'somchai01',
            'email'               => 'somchai@test.com',
            'password'            => 'Password1!',
            'password_confirmation' => 'Password1!',
            'first_name_th'       => 'สมชาย',
            'last_name_th'        => 'ใจดี',
            'first_name_en'       => 'Somchai',
            'last_name_en'        => 'Jaidee',
            'date_of_birth'       => '2010-01-15',
            'age'                 => 16,
            'grade_level'         => 'ม.4',
            'advisor_teacher_id'  => $teacher->id,
            'phone'               => '0812345678',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'user'])
            ->assertJsonPath('user.role', 'student')
            ->assertJsonPath('user.status', 'pending');

        $this->assertDatabaseHas('users', ['username' => 'somchai01', 'role' => 'student']);
        $this->assertDatabaseHas('students', ['first_name_th' => 'สมชาย', 'status' => 'pending']);
    }

    // TC-AUTH-002
    public function test_register_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'somchai01']);

        $response = $this->postJson('/api/auth/register', [
            'username'   => 'somchai01',
            'email'      => 'other@test.com',
            'password'   => 'Password1!',
            'password_confirmation' => 'Password1!',
            'first_name_th' => 'Test', 'last_name_th' => 'User',
            'first_name_en' => 'Test', 'last_name_en' => 'User',
            'date_of_birth' => '2010-01-01', 'age' => 16, 'grade_level' => 'ม.4',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    // TC-AUTH-003
    public function test_register_fails_with_weak_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'newuser01', 'email' => 'new@test.com',
            'password' => '123456', 'password_confirmation' => '123456',
            'first_name_th' => 'Test', 'last_name_th' => 'User',
            'first_name_en' => 'Test', 'last_name_en' => 'User',
            'date_of_birth' => '2010-01-01', 'age' => 16, 'grade_level' => 'ม.4',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // TC-AUTH-004
    public function test_admin_can_login(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => 'AdminPass1!',
            'role'     => 'admin',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'admin',
            'password' => 'AdminPass1!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user'])
            ->assertJsonPath('user.role', 'admin');
    }

    // TC-AUTH-005
    public function test_approved_student_can_login(): void
    {
        $user = User::factory()->create([
            'username' => 'somchai01',
            'password' => 'Password1!',
            'role'     => 'student',
        ]);
        Student::factory()->create(['user_id' => $user->id, 'status' => 'approved']);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'somchai01',
            'password' => 'Password1!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token'])
            ->assertJsonPath('user.student_status', 'approved');
    }

    // TC-AUTH-006
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['username' => 'testuser', 'password' => 'CorrectPass1!']);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'testuser',
            'password' => 'WrongPass',
        ]);

        $response->assertStatus(401);
    }

    // TC-AUTH-007
    public function test_login_fails_for_inactive_account(): void
    {
        User::factory()->create([
            'username' => 'inactive01', 'password' => 'Password1!', 'is_active' => false
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'inactive01', 'password' => 'Password1!',
        ]);

        $response->assertStatus(403);
    }

    // TC-AUTH-008
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'username' => 'logoutuser',
            'password' => 'Password1!',
            'is_active' => true,
        ]);

        $token = $this->postJson('/api/auth/login', [
            'username' => 'logoutuser',
            'password' => 'Password1!',
        ])->json('token');

        $response = $this->withToken($token)->postJson('/api/auth/logout');
        $response->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }
}
