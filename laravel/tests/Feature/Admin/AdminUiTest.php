<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_login_form_for_admin_teacher_and_student(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('เข้าสู่ระบบ')
            ->assertSee('Admin')
            ->assertSee('Teacher')
            ->assertSee('Student')
            ->assertSee('name="username"', false)
            ->assertSee('value="admin"', false)
            ->assertSee('name="password"', false)
            ->assertSee('href="/register"', false)
            ->assertSee('ลงทะเบียน')
            ->assertSee('Admin1234!')
            ->assertSee('Teacher1234!')
            ->assertSee('Student1234!');
    }

    public function test_seeded_admin_credentials_can_login_through_api(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@studentschool.ac.th',
            'password' => 'Admin1234!',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->postJson('/api/auth/login', [
            'username' => 'admin',
            'password' => 'Admin1234!',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user'])
            ->assertJsonPath('user.username', 'admin')
            ->assertJsonPath('user.role', 'admin');
    }

    public function test_admin_spa_routes_render_sidebar_menu(): void
    {
        foreach (['/admin', '/admin/teachers', '/admin/subjects', '/admin/assignments', '/admin/students'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('id="admin-app"', false)
                ->assertSee('Admin Menu')
                ->assertSee('menuItems');
        }
    }
}
