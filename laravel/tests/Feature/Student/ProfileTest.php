<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Student $student;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'student',
            'password' => 'Password1!',
            'email' => 'old@example.test',
        ]);
        $this->student = Student::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'approved',
            'email' => 'old@example.test',
        ]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_student_can_view_own_profile(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/student/profile');

        $response->assertOk()
            ->assertJsonPath('student.id', $this->student->id)
            ->assertJsonPath('student.user.id', $this->user->id);
    }

    public function test_student_can_update_profile_fields_and_email(): void
    {
        $advisor = Teacher::factory()->create();

        $response = $this->withToken($this->token)->putJson('/api/student/profile', [
            'first_name_th' => 'สมชาย',
            'last_name_th' => 'ทดสอบ',
            'first_name_en' => 'Somchai',
            'last_name_en' => 'Test',
            'date_of_birth' => '2010-01-01',
            'age' => 16,
            'grade_level' => 'ม.4',
            'advisor_teacher_id' => $advisor->id,
            'phone' => '0999888777',
            'email' => 'new@example.test',
        ]);

        $response->assertOk()
            ->assertJsonPath('student.phone', '0999888777')
            ->assertJsonPath('student.email', 'new@example.test');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => 'new@example.test',
        ]);
    }

    public function test_student_cannot_update_profile_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        $response = $this->withToken($this->token)->putJson('/api/student/profile', [
            'email' => 'taken@example.test',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_student_can_change_password_with_current_password(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/student/profile', [
            'current_password' => 'Password1!',
            'new_password' => 'NewPassword1!',
            'new_password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('NewPassword1!', $this->user->fresh()->password));
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/student/profile', [
            'current_password' => 'WrongPassword1!',
            'new_password' => 'NewPassword1!',
            'new_password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('message', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
    }
}
