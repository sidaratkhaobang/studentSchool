<?php

namespace Tests\Feature\Student;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_spa_routes_render_student_menu(): void
    {
        foreach (['/student', '/student/dashboard', '/student/enrollment', '/student/profile'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('id="student-app"', false)
                ->assertSee('ตารางเรียนของฉัน')
                ->assertSee('ลงทะเบียนเรียน')
                ->assertSee('โปรไฟล์ของฉัน');
        }
    }
}
