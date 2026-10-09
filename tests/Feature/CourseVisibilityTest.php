<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'StrongPassword123',
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    private function createDraftCourse(User $owner): Course
    {
        return Course::create([
            'user_id' => $owner->id,
            'title' => 'Private draft course',
            'slug' => 'private-draft-course',
            'description' => 'A course that has not been published yet.',
            'price' => 10,
            'status' => 'draft',
        ]);
    }

    public function test_another_user_cannot_like_a_draft_course(): void
    {
        $owner = $this->createUser('owner@example.com');
        $otherUser = $this->createUser('other@example.com');
        $course = $this->createDraftCourse($owner);

        $this->actingAs($otherUser)
            ->postJson('/api/courses/'.$course->id.'/like')
            ->assertNotFound();

        $this->assertDatabaseMissing('course_likes', [
            'course_id' => $course->id,
            'user_id' => $otherUser->id,
        ]);
    }

    public function test_another_user_cannot_comment_on_a_draft_course(): void
    {
        $owner = $this->createUser('owner@example.com');
        $otherUser = $this->createUser('other@example.com');
        $course = $this->createDraftCourse($owner);

        $this->actingAs($otherUser)
            ->postJson('/api/courses/'.$course->id.'/comment', [
                'content' => 'This should not be allowed.',
            ])
            ->assertNotFound();

        $this->assertDatabaseMissing('course_comments', [
            'course_id' => $course->id,
            'user_id' => $otherUser->id,
        ]);
    }
}
