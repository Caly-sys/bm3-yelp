<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Notifications\InvitationAcceptedNotification;
use App\Notifications\NewReviewNotification;
use App\Notifications\UserSuspendedNotification;
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

    public function test_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new UserSuspendedNotification($user, false));

        $notification = $user->notifications()->first();
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->post(route('notifications.markAsRead', $notification->id));

        $response->assertRedirect();
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_delete_single_notification(): void
    {
        $user = User::factory()->create();
        $user->notify(new UserSuspendedNotification($user, false));

        $notification = $user->notifications()->first();
        $notifId = $notification->id;

        $response = $this->actingAs($user)->delete(route('notifications.destroy', $notifId));

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notifId]);
    }

    public function test_delete_all_notifications(): void
    {
        $user = User::factory()->create();
        $user->notify(new UserSuspendedNotification($user, false));
        $user->notify(new UserSuspendedNotification($user, true));

        $this->assertEquals(2, $user->notifications()->count());

        $response = $this->actingAs($user)->delete(route('notifications.destroyAll'));

        $response->assertRedirect();
        $this->assertEquals(0, $user->fresh()->notifications()->count());
    }

    public function test_notifications_page_loads(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    public function test_user_cannot_read_another_users_notification(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->notify(new UserSuspendedNotification($user1, false));
        $notification = $user1->notifications()->first();

        // User2 tries to mark user1's notification as read
        $response = $this->actingAs($user2)->post(route('notifications.markAsRead', $notification->id));
        $response->assertStatus(404);
    }

    public function test_user_cannot_delete_another_users_notification(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->notify(new UserSuspendedNotification($user1, false));
        $notification = $user1->notifications()->first();

        $response = $this->actingAs($user2)->delete(route('notifications.destroy', $notification->id));
        $response->assertStatus(404);
    }

    public function test_notification_persists_in_database(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $user->notify(new AccountCreatedNotification($user, $admin));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'type' => 'App\Notifications\AccountCreatedNotification',
        ]);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'new_test_student',
            'name' => 'New Test Student',
            'email' => 'new_test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'student',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'new_test_student',
            'role' => 'student',
        ]);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->post(route('admin.users.store'), [
            'username' => 'hacked_admin',
            'name' => 'Hacked Admin',
            'email' => 'hacked@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['username' => 'hacked_admin']);
    }

    public function test_teacher_cannot_create_user_via_admin_route(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->post(route('admin.users.store'), [
            'username' => 'teacher_hack',
            'name' => 'Teacher Hack',
            'email' => 'teacher_hack@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
    }

    public function test_suspension_sends_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.toggle-suspend', $student));

        $response->assertRedirect();
        $student->refresh();
        $this->assertTrue($student->is_suspended);

        $notif = $student->notifications()->where('type', 'App\Notifications\UserSuspendedNotification')->first();
        $this->assertNotNull($notif);
    }

    public function test_teacher_can_create_student_invitation(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->post(route('invitations.store'), [
            'email' => 'invited_student@example.com',
            'name' => 'Invited Student',
            'role' => 'student',
        ]);

        $response->assertRedirect(route('invitations.index'));
        $this->assertDatabaseHas('invitations', [
            'email' => 'invited_student@example.com',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'pending',
        ]);
    }

    public function test_teacher_cannot_invite_teacher(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->post(route('invitations.store'), [
            'email' => 'another_teacher@example.com',
            'name' => 'Another Teacher',
            'role' => 'teacher',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('invitations', ['email' => 'another_teacher@example.com']);
    }

    public function test_admin_can_invite_teacher(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('invitations.store'), [
            'email' => 'new_teacher@example.com',
            'name' => 'New Teacher',
            'role' => 'teacher',
        ]);

        $response->assertRedirect(route('invitations.index'));
        $this->assertDatabaseHas('invitations', [
            'email' => 'new_teacher@example.com',
            'role' => 'teacher',
            'status' => 'pending',
        ]);
    }

    public function test_student_cannot_create_invitation(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->post(route('invitations.store'), [
            'email' => 'test@example.com',
            'name' => 'Test',
            'role' => 'student',
        ]);

        $response->assertStatus(403);
    }

    public function test_invitation_acceptance_creates_account(): void
    {
        $teacher = User::factory()->teacher()->create();

        $invitation = Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => 'accept_test@example.com',
            'name' => 'Accept Test',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Show accept form
        $response = $this->get(route('invitations.accept', $invitation->token));
        $response->assertStatus(200);
        $response->assertSeeText('You\'re Invited');

        // Accept the invitation
        $response = $this->post(route('invitations.accept.process', $invitation->token), [
            'username' => 'accepted_student',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'username' => 'accepted_student',
            'email' => 'accept_test@example.com',
            'role' => 'student',
        ]);

        $invitation->refresh();
        $this->assertEquals('accepted', $invitation->status);
    }

    public function test_expired_invitation_cannot_be_accepted(): void
    {
        $teacher = User::factory()->teacher()->create();

        $invitation = Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => 'expired_test@example.com',
            'name' => 'Expired Test',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'pending',
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('invitations.accept', $invitation->token));
        $response->assertStatus(200);
        $response->assertSee('expired');
    }

    public function test_used_invitation_cannot_be_reused(): void
    {
        $teacher = User::factory()->teacher()->create();

        $invitation = Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => 'used_test@example.com',
            'name' => 'Used Test',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('invitations.accept', $invitation->token));
        $response->assertStatus(200);
        $response->assertSee('already been used');
    }

    public function test_duplicate_invitation_prevented(): void
    {
        $teacher = User::factory()->teacher()->create();

        Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => 'dup_test@example.com',
            'name' => 'Dup Test',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($teacher)->post(route('invitations.store'), [
            'email' => 'dup_test@example.com',
            'name' => 'Dup Test 2',
            'role' => 'student',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_existing_email_invitation_prevented(): void
    {
        $teacher = User::factory()->teacher()->create();
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($teacher)->post(route('invitations.store'), [
            'email' => 'existing@example.com',
            'name' => 'Existing User',
            'role' => 'student',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_invitation_can_be_cancelled(): void
    {
        $teacher = User::factory()->teacher()->create();

        $invitation = Invitation::create([
            'token' => Invitation::generateToken(),
            'email' => 'cancel_test@example.com',
            'name' => 'Cancel Test',
            'role' => 'student',
            'invited_by' => $teacher->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($teacher)->post(route('invitations.cancel', $invitation));

        $response->assertRedirect();
        $invitation->refresh();
        $this->assertEquals('cancelled', $invitation->status);
    }

    public function test_unread_count_updates_correctly(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->assertEquals(0, $user->unreadNotifications()->count());

        $user->notify(new AccountCreatedNotification($user, $admin));
        $user->refresh();
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $user->notify(new UserSuspendedNotification($user, true));
        $user->refresh();
        $this->assertEquals(2, $user->unreadNotifications()->count());

        $user->unreadNotifications->first()->markAsRead();
        $user->refresh();
        $this->assertEquals(1, $user->unreadNotifications()->count());
    }
}
