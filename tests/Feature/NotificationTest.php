<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\NewReviewNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_sent_when_review_posted_for_linked_teacher(): void
    {
        Notification::fake();

        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('reviews.store', $teacher), [
            'overall_rating' => 5,
            'teaching_rating' => 5,
            'explanation_rating' => 5,
            'fairness_rating' => 5,
            'workload_rating' => 5,
            'comment' => 'This teacher is absolutely amazing and inspiring!',
        ]);

        Notification::assertSentTo($teacherUser, NewReviewNotification::class);
    }

    public function test_no_notification_when_teacher_has_no_linked_account(): void
    {
        Notification::fake();

        $teacher = Teacher::factory()->create(['user_id' => null]);
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('reviews.store', $teacher), [
            'overall_rating' => 4,
            'teaching_rating' => 4,
            'explanation_rating' => 4,
            'fairness_rating' => 4,
            'workload_rating' => 4,
            'comment' => 'A very good teacher with great explanations.',
        ]);

        Notification::assertNothingSent();
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $student = User::factory()->create();

        // Create a review to generate a notification
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);
        $teacherUser->notify(new NewReviewNotification($review, $student));

        $this->assertEquals(1, $teacherUser->unreadNotifications()->count());

        // Mark all as read
        $response = $this->actingAs($teacherUser)->post(route('notifications.markAllRead'));

        $response->assertRedirect();
        $this->assertEquals(0, $teacherUser->fresh()->unreadNotifications()->count());
    }
}
