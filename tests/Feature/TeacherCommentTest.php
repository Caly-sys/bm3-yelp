<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\ReviewComment;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_respond_to_review_on_their_profile(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $student = User::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($teacherUser)->post(route('reviews.comments.store', $review), [
            'content' => 'Thank you for your feedback!',
        ]);

        $response->assertRedirect(route('teachers.show', $teacher));
        $this->assertDatabaseHas('review_comments', [
            'review_id' => $review->id,
            'user_id' => $teacherUser->id,
            'content' => 'Thank you for your feedback!',
        ]);
    }

    public function test_non_teacher_cannot_respond_to_review(): void
    {
        $teacher = Teacher::factory()->create();
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($anotherStudent)->post(route('reviews.comments.store', $review), [
            'content' => 'I should not be able to post this.',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('review_comments', [
            'review_id' => $review->id,
            'user_id' => $anotherStudent->id,
        ]);
    }

    public function test_admin_can_respond_to_any_review(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = Teacher::factory()->create();
        $student = User::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($admin)->post(route('reviews.comments.store', $review), [
            'content' => 'Admin moderation response.',
        ]);

        $response->assertRedirect(route('teachers.show', $teacher));
        $this->assertDatabaseHas('review_comments', [
            'review_id' => $review->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_teacher_cannot_respond_to_review_on_different_profile(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $otherTeacher = Teacher::factory()->create(); // no linked account
        $student = User::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $otherTeacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($teacherUser)->post(route('reviews.comments.store', $review), [
            'content' => 'I should not be able to respond to a different teacher\'s reviews.',
        ]);

        $response->assertStatus(403);
    }
}
