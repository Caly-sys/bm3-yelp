<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Bm3PlatformTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Public browsing tests
     */
    public function test_guest_can_view_home_page(): void
    {
        Teacher::factory(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Find & Review Your');
        $response->assertSeeText('SMK Bina Mandiri Multimedia');
    }

    public function test_guest_can_view_teacher_directory_and_filter(): void
    {
        $teacherWeb = Teacher::factory()->create([
            'name' => 'Pak Ahmad Hidayat',
            'subject' => 'Pemrograman Web',
        ]);
        $teacherMath = Teacher::factory()->create([
            'name' => 'Bu Siti Rahayu',
            'subject' => 'Matematika',
        ]);

        $response = $this->get('/teachers');
        $response->assertStatus(200);
        $response->assertSee('Pak Ahmad Hidayat');
        $response->assertSee('Bu Siti Rahayu');

        // Search test
        $searchResponse = $this->get('/teachers?search=Ahmad');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Pak Ahmad Hidayat');
        $searchResponse->assertDontSee('Bu Siti Rahayu');

        // Subject filter test
        $filterResponse = $this->get('/teachers?subject=Matematika');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Bu Siti Rahayu');
        $filterResponse->assertDontSee('Pak Ahmad Hidayat');
    }

    public function test_guest_can_view_teacher_profile_with_ratings(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Pak Budi Santoso']);
        $student = User::factory()->create(['username' => 'student_reviewer']);

        Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
            'overall_rating' => 5,
            'teaching_rating' => 5,
            'explanation_rating' => 4,
            'fairness_rating' => 5,
            'workload_rating' => 3,
            'comment' => 'Penjelasan sangat bagus dan mudah dipahami.',
        ]);

        $response = $this->get("/teachers/{$teacher->id}");

        $response->assertStatus(200);
        $response->assertSee('Pak Budi Santoso');
        $response->assertSee('@student_reviewer');
        $response->assertSee('Penjelasan sangat bagus dan mudah dipahami.');
    }

    /**
     * 2. Review and Rating creation & policy tests
     */
    public function test_student_can_create_a_review(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($student)->post("/teachers/{$teacher->id}/reviews", [
            'overall_rating' => 5,
            'teaching_rating' => 5,
            'explanation_rating' => 4,
            'fairness_rating' => 5,
            'workload_rating' => 4,
            'comment' => 'Guru ini sangat ramah dan materi disampaikan dengan sangat jelas.',
        ]);

        $response->assertRedirect("/teachers/{$teacher->id}");
        $this->assertDatabaseHas('reviews', [
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
            'overall_rating' => 5,
        ]);
    }

    public function test_student_cannot_submit_duplicate_review_for_same_teacher(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();

        Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($student)->post("/teachers/{$teacher->id}/reviews", [
            'overall_rating' => 4,
            'teaching_rating' => 4,
            'explanation_rating' => 4,
            'fairness_rating' => 4,
            'workload_rating' => 4,
            'comment' => 'Percobaan review kedua yang seharusnya ditolak oleh sistem.',
        ]);

        $response->assertRedirect("/teachers/{$teacher->id}");
        $this->assertCount(1, Review::where('teacher_id', $teacher->id)->where('user_id', $student->id)->get());
    }

    public function test_student_can_edit_own_review(): void
    {
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
            'comment' => 'Komentar awal sebelum diedit oleh siswa.',
        ]);

        $response = $this->actingAs($student)->put("/reviews/{$review->id}", [
            'overall_rating' => 5,
            'teaching_rating' => 5,
            'explanation_rating' => 5,
            'fairness_rating' => 5,
            'workload_rating' => 5,
            'comment' => 'Komentar baru yang sudah diperbarui dengan lebih lengkap.',
        ]);

        $response->assertRedirect("/teachers/{$teacher->id}");
        $review->refresh();
        $this->assertSame('Komentar baru yang sudah diperbarui dengan lebih lengkap.', $review->comment);
    }

    public function test_student_cannot_edit_another_users_review(): void
    {
        $student1 = User::factory()->create();
        $student2 = User::factory()->create();
        $teacher = Teacher::factory()->create();

        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student1->id,
            'comment' => 'Review asli dari siswa 1.',
        ]);

        $response = $this->actingAs($student2)->put("/reviews/{$review->id}", [
            'overall_rating' => 1,
            'teaching_rating' => 1,
            'explanation_rating' => 1,
            'fairness_rating' => 1,
            'workload_rating' => 1,
            'comment' => 'Percobaan mengedit review orang lain secara tidak sah.',
        ]);

        $response->assertStatus(403);
    }

    /**
     * 3. Helpful votes tests
     */
    public function test_student_can_toggle_helpful_vote_on_review(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $author->id,
        ]);

        // Toggle vote ON
        $response = $this->actingAs($voter)->postJson("/reviews/{$review->id}/vote");
        $response->assertOk();
        $response->assertJson(['voted' => true, 'count' => 1]);
        $this->assertDatabaseHas('review_votes', [
            'review_id' => $review->id,
            'user_id' => $voter->id,
        ]);

        // Toggle vote OFF
        $response2 = $this->actingAs($voter)->postJson("/reviews/{$review->id}/vote");
        $response2->assertOk();
        $response2->assertJson(['voted' => false, 'count' => 0]);
        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $review->id,
            'user_id' => $voter->id,
        ]);
    }

    public function test_user_cannot_vote_on_own_review(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson("/reviews/{$review->id}/vote");
        $response->assertStatus(422);
    }

    /**
     * 4. Reports & Moderation tests
     */
    public function test_student_can_report_review(): void
    {
        $author = User::factory()->create();
        $reporter = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $author->id,
        ]);

        $response = $this->actingAs($reporter)->postJson("/reviews/{$review->id}/report", [
            'reason' => 'spam',
            'details' => 'Review ini tidak relevan.',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('reports', [
            'review_id' => $review->id,
            'user_id' => $reporter->id,
            'reason' => 'spam',
            'status' => 'pending',
        ]);
    }

    /**
     * 5. Admin authorization and actions tests
     */
    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_manage_teachers(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();
        $response->assertSee('Admin Dashboard');

        // Create teacher
        $createResponse = $this->actingAs($admin)->post('/admin/teachers', [
            'name' => 'Pak Guru Baru',
            'subject' => 'Animasi 3D',
            'description' => 'Guru animasi berpengalaman.',
        ]);
        $createResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['name' => 'Pak Guru Baru']);

        // Delete teacher
        $teacher = Teacher::where('name', 'Pak Guru Baru')->first();
        $deleteResponse = $this->actingAs($admin)->delete("/admin/teachers/{$teacher->id}");
        $deleteResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_admin_can_resolve_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $review = Review::factory()->create([
            'teacher_id' => $teacher->id,
            'user_id' => $student->id,
        ]);

        $report = Report::create([
            'review_id' => $review->id,
            'user_id' => $student->id,
            'reason' => 'offensive',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put("/admin/reports/{$report->id}/resolve");
        $response->assertRedirect();

        $review->refresh();
        $this->assertSame('hidden', $review->status);
        $report->refresh();
        $this->assertSame('resolved', $report->status);
    }
}
