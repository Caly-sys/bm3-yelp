<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'username' => 'admin',
            'name' => 'Admin BM3',
            'email' => 'admin@bm3.sch.id',
        ]);

        // Create a demo student account
        $demoStudent = User::factory()->create([
            'username' => 'student_demo',
            'name' => 'Demo Student',
            'email' => 'student@bm3.sch.id',
        ]);

        // Create 30 regular students
        $students = User::factory(30)->create();

        // Add the demo student to the collection
        $allStudents = $students->push($demoStudent);

        // Create 15 teachers with Indonesian names and BM3 subjects
        $teachers = Teacher::factory(15)->create();

        // Create specific well-known teachers with linked user accounts
        $specificTeachersData = [
            [
                'teacher' => [
                    'name' => 'Pak Ahmad Hidayat',
                    'subject' => 'Pemrograman Web',
                    'description' => 'Guru senior pemrograman web dengan pengalaman 10 tahun di industri IT. Mengajarkan HTML, CSS, JavaScript, dan PHP/Laravel.',
                ],
                'user' => [
                    'username' => 'pak_ahmad',
                    'name' => 'Pak Ahmad Hidayat',
                    'email' => 'pak_ahmad@bm3.sch.id',
                ],
            ],
            [
                'teacher' => [
                    'name' => 'Bu Siti Rahayu',
                    'subject' => 'Desain Grafis',
                    'description' => 'Spesialis desain grafis dan multimedia. Menguasai Adobe Creative Suite dan berbagai tools desain modern.',
                ],
                'user' => [
                    'username' => 'bu_siti',
                    'name' => 'Bu Siti Rahayu',
                    'email' => 'bu_siti@bm3.sch.id',
                ],
            ],
            [
                'teacher' => [
                    'name' => 'Pak Budi Santoso',
                    'subject' => 'Matematika',
                    'description' => 'Guru matematika yang dikenal dengan metode pengajaran interaktif dan pendekatan problem-solving.',
                ],
                'user' => [
                    'username' => 'pak_budi',
                    'name' => 'Pak Budi Santoso',
                    'email' => 'pak_budi@bm3.sch.id',
                ],
            ],
            [
                'teacher' => [
                    'name' => 'Bu Dewi Lestari',
                    'subject' => 'Bahasa Inggris',
                    'description' => 'Berpengalaman mengajar Bahasa Inggris dengan fokus pada conversation dan business English.',
                ],
                'user' => [
                    'username' => 'bu_dewi',
                    'name' => 'Bu Dewi Lestari',
                    'email' => 'bu_dewi@bm3.sch.id',
                ],
            ],
            [
                'teacher' => [
                    'name' => 'Pak Riko Pratama',
                    'subject' => 'Jaringan Komputer',
                    'description' => 'Certified network engineer. Mengajarkan jaringan komputer dari dasar hingga konfigurasi enterprise.',
                ],
                'user' => [
                    'username' => 'pak_riko',
                    'name' => 'Pak Riko Pratama',
                    'email' => 'pak_riko@bm3.sch.id',
                ],
            ],
        ];

        $specificTeachers = collect();
        foreach ($specificTeachersData as $data) {
            // Create the teacher's user account
            $teacherUser = User::factory()->teacher()->create($data['user']);

            // Create the teacher profile linked to the user account
            $teacher = Teacher::factory()->create(array_merge($data['teacher'], [
                'user_id' => $teacherUser->id,
            ]));

            $specificTeachers->push($teacher);
        }

        $allTeachers = $teachers->concat($specificTeachers);

        // Create reviews spread across teachers
        // Ensure unique user-teacher pairs
        $usedPairs = [];

        foreach ($allTeachers as $teacher) {
            // Each teacher gets 3-8 reviews
            $reviewCount = fake()->numberBetween(3, 8);
            $reviewers = $allStudents->random(min($reviewCount, $allStudents->count()));

            foreach ($reviewers as $reviewer) {
                $pairKey = $teacher->id . '-' . $reviewer->id;
                if (in_array($pairKey, $usedPairs)) {
                    continue;
                }
                $usedPairs[] = $pairKey;

                $review = Review::factory()->create([
                    'teacher_id' => $teacher->id,
                    'user_id' => $reviewer->id,
                ]);

                // Some reviews get helpful votes
                if (fake()->boolean(60)) {
                    $voterCount = fake()->numberBetween(1, 8);
                    $voters = $allStudents->except($reviewer->id)->random(min($voterCount, $allStudents->count() - 1));

                    foreach ($voters as $voter) {
                        ReviewVote::firstOrCreate([
                            'review_id' => $review->id,
                            'user_id' => $voter->id,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Seeded: 1 admin, 31 students, 5 teacher accounts, ' . $allTeachers->count() . ' teachers, ' . Review::count() . ' reviews, ' . ReviewVote::count() . ' votes');
    }
}
