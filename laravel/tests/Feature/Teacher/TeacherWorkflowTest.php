<?php

namespace Tests\Feature\Teacher;

use App\Models\EnrollmentCourse;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WeeklyEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_login_and_access_dashboard(): void
    {
        [$teacherUser, $teacher] = $this->createTeacherUser();
        $subject = Subject::factory()->create(['name_th' => 'คณิตศาสตร์']);
        SubjectTeacher::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'is_primary' => true]);

        Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'advisor_teacher_id' => $teacher->id,
            'grade_level' => 'ม.4/1',
            'status' => 'approved',
        ]);

        $login = $this->postJson('/api/auth/login', [
            'username' => $teacherUser->username,
            'password' => 'Teacher1234!',
        ]);

        $login->assertOk()
            ->assertJsonPath('user.role', 'teacher')
            ->assertJsonPath('user.teacher.id', $teacher->id);

        $token = $login->json('token');

        $this->withToken($token)->getJson('/api/teacher/dashboard')
            ->assertOk()
            ->assertJsonPath('teacher.id', $teacher->id)
            ->assertJsonPath('stats.subjects', 1)
            ->assertJsonPath('stats.advising_students', 1)
            ->assertJsonPath('classrooms.0.grade_level', 'ม.4/1');
    }

    public function test_advisor_teacher_can_approve_submitted_enrollment(): void
    {
        [$teacherUser, $teacher] = $this->createTeacherUser();
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'advisor_teacher_id' => $teacher->id,
            'status' => 'approved',
        ]);
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        $token = $teacherUser->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson("/api/teacher/enrollments/{$enrollment->id}/status", [
            'status' => 'approved',
        ])
            ->assertOk()
            ->assertJsonPath('enrollment.status', 'approved')
            ->assertJsonPath('enrollment.approved_by_teacher_id', $teacher->id);

        $this->assertDatabaseHas('weekly_enrollments', [
            'id' => $enrollment->id,
            'status' => 'approved',
            'approved_by_teacher_id' => $teacher->id,
        ]);
    }

    public function test_teacher_cannot_approve_enrollment_outside_advising_classroom(): void
    {
        [$teacherUser] = $this->createTeacherUser();
        $otherTeacher = Teacher::factory()->create();
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'advisor_teacher_id' => $otherTeacher->id,
            'status' => 'approved',
        ]);
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        $this->withToken($teacherUser->createToken('test')->plainTextToken)
            ->putJson("/api/teacher/enrollments/{$enrollment->id}/status", ['status' => 'approved'])
            ->assertForbidden();
    }

    public function test_teacher_can_reject_submitted_enrollment_with_reason(): void
    {
        [$teacherUser, $teacher] = $this->createTeacherUser();
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'advisor_teacher_id' => $teacher->id,
            'status' => 'approved',
        ]);
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        $this->withToken($teacherUser->createToken('test')->plainTextToken)
            ->putJson("/api/teacher/enrollments/{$enrollment->id}/status", [
                'status' => 'rejected',
                'rejection_reason' => 'จำนวนชั่วโมงยังไม่เหมาะสม',
            ])
            ->assertOk()
            ->assertJsonPath('enrollment.status', 'rejected')
            ->assertJsonPath('enrollment.rejection_reason', 'จำนวนชั่วโมงยังไม่เหมาะสม');
    }

    public function test_teacher_can_manage_content_for_assigned_subject(): void
    {
        Storage::fake('public');
        [$teacherUser, $teacher] = $this->createTeacherUser();
        $subject = Subject::factory()->create();
        SubjectTeacher::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        $this->withToken($teacherUser->createToken('test')->plainTextToken)
            ->postJson("/api/teacher/subjects/{$subject->id}/content", [
                'learning_content' => 'บทที่ 1 จำนวนจริง',
                'material_file' => UploadedFile::fake()->create('lesson.pdf', 120, 'application/pdf'),
            ])
            ->assertOk()
            ->assertJsonPath('subject.learning_content', 'บทที่ 1 จำนวนจริง');

        $this->assertNotNull($subject->fresh()->material_path);
        Storage::disk('public')->assertExists($subject->fresh()->material_path);
    }

    public function test_teacher_spa_routes_render_teacher_menu(): void
    {
        foreach (['/teacher', '/teacher/dashboard', '/teacher/enrollments', '/teacher/subjects'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('id="teacher-app"', false)
                ->assertSee('แดชบอร์ดอาจารย์')
                ->assertSee('อนุมัติตารางเรียน')
                ->assertSee('รายวิชาที่รับผิดชอบ');
        }
    }

    private function createTeacherUser(): array
    {
        $user = User::factory()->create([
            'username' => 'teacher01',
            'email' => 'teacher01@school.ac.th',
            'password' => 'Teacher1234!',
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $teacher = Teacher::factory()->create([
            'user_id' => $user->id,
            'email' => 'teacher.profile@school.ac.th',
        ]);

        return [$user, $teacher];
    }
}
