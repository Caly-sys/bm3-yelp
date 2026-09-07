<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_user_can_be_created_with_teacher_role(): void
    {
        $user = User::factory()->teacher()->create();

        $this->assertEquals('teacher', $user->role);
        $this->assertTrue($user->isTeacher());
        $this->assertFalse($user->isAdmin());
    }

    public function test_teacher_profile_can_be_linked_to_user_account(): void
    {
        $user = User::factory()->teacher()->create();

        $teacher = Teacher::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $teacher->user_id);
        $this->assertEquals($teacher->id, $user->teacher->id);
        $this->assertEquals($user->id, $teacher->user->id);
    }

    public function test_teacher_without_linked_account_returns_null(): void
    {
        $teacher = Teacher::factory()->create([
            'user_id' => null,
        ]);

        $this->assertNull($teacher->user);
    }

    public function test_student_is_not_teacher(): void
    {
        $user = User::factory()->create(); // default role is 'student'

        $this->assertFalse($user->isTeacher());
        $this->assertNull($user->teacher);
    }
}
