<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_delete_own_review(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($student)->delete(route('reviews.destroy', $review));

        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_admin_can_delete_any_review(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('teachers.show', $teacher));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_student_cannot_submit_duplicate_review(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();

        // First review
        Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        // Attempt duplicate review
        $response = $this->actingAs($student)->post(route('reviews.store', $teacher), [
            'overall_rating' => 4,
            'teaching_rating' => 4,
            'explanation_rating' => 4,
            'fairness_rating' => 4,
            'workload_rating' => 4,
            'comment' => 'This is a duplicate review that should be rejected.',
        ]);

        $response->assertRedirect(route('teachers.show', $teacher));
        $response->assertSessionHas('error');

        // Should still only have 1 review
        $this->assertEquals(1, Review::where('teacher_id', $teacher->id)->where('user_id', $student->id)->count());
    }

    public function test_already_reviewed_teacher_shows_disabled_button(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();
        Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($student)->get(route('teachers.show', $teacher));

        $response->assertStatus(200);
        $response->assertSee('Already Reviewed');
    }
}
