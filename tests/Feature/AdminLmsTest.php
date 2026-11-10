<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLmsTest extends TestCase
{
    public function test_admin_can_authenticate_with_default_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
            'form_loaded_at' => time() - 5,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    public function test_admin_can_access_admin_dashboard_and_curriculum()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Dashboard
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Management');

        // Course list
        $courseResponse = $this->get('/admin/courses');
        $courseResponse->assertStatus(200);

        // Course lessons
        $course = Course::first();
        $lessonsResponse = $this->get("/admin/courses/{$course->id}/lessons");
        $lessonsResponse->assertStatus(200);
        $lessonsResponse->assertSee('Kurikulum');

        // Submissions review
        $submissionsResponse = $this->get('/admin/submissions');
        $submissionsResponse->assertStatus(200);
        $submissionsResponse->assertSee('Review');
    }

    public function test_course_contains_all_requested_material_types()
    {
        $course = Course::first();
        $types = $course->lessons->pluck('type')->unique()->toArray();

        $this->assertContains('pre_test', $types);
        $this->assertContains('video', $types);
        $this->assertContains('pdf', $types);
        $this->assertContains('assignment', $types);
        $this->assertContains('post_test', $types);
    }
}
