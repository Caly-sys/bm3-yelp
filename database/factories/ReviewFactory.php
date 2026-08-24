<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Sample review comments (neutral, clearly demo data).
     */
    private static array $comments = [
        'Penjelasan materi sangat jelas dan mudah dipahami. Saya suka cara mengajarnya yang interaktif.',
        'Guru yang sangat berpengalaman. Selalu memberikan contoh yang relevan dengan kehidupan nyata.',
        'Cara mengajar cukup baik, tapi kadang terlalu cepat. Perlu lebih sabar menjelaskan materi yang sulit.',
        'Sangat membantu siswa yang kesulitan. Selalu bersedia menjelaskan ulang di luar jam pelajaran.',
        'Tugasnya banyak tapi bermanfaat. Membuat saya lebih memahami materi dengan baik.',
        'Metode pengajaran yang menarik. Sering menggunakan media visual dan praktik langsung.',
        'Guru yang adil dalam penilaian. Tidak pilih kasih dan selalu objektif.',
        'Penjelasan kadang membingungkan, tapi jika ditanya lagi akan menjelaskan dengan lebih detail.',
        'Sangat menguasai materi pelajaran. Bisa menjawab semua pertanyaan siswa dengan baik.',
        'Atmosfer kelas sangat menyenangkan. Tidak membuat siswa tegang atau takut bertanya.',
        'Memberikan tugas yang cukup menantang. Bagus untuk melatih kemampuan problem solving.',
        'Cara mengajar sudah baik. Akan lebih baik lagi jika ada lebih banyak contoh praktis.',
        'Guru favorit saya! Selalu bisa membuat materi yang sulit menjadi mudah dipahami.',
        'Penilaian sangat transparan. Siswa selalu tahu kriteria penilaian sebelum ujian.',
        'Penggunaan teknologi dalam pembelajaran sangat baik. Sering menggunakan presentasi dan video.',
        'Waktu mengajar selalu tepat. Tidak pernah terlambat dan selalu memaksimalkan waktu kelas.',
        'Memberikan feedback yang konstruktif pada setiap tugas. Sangat membantu untuk perbaikan.',
        'Materi yang diajarkan relevan dengan kebutuhan industri. Sangat berguna untuk karir ke depan.',
        'Sabar dalam menghadapi pertanyaan siswa, bahkan yang paling basic sekalipun.',
        'Guru yang inspiratif. Sering berbagi pengalaman yang memotivasi siswa.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate correlated ratings (not purely random, feels more realistic)
        $baseRating = fake()->numberBetween(3, 5);
        $variance = fn () => max(1, min(5, $baseRating + fake()->numberBetween(-1, 1)));

        return [
            'teacher_id' => Teacher::factory(),
            'user_id' => User::factory(),
            'overall_rating' => $baseRating,
            'teaching_rating' => $variance(),
            'explanation_rating' => $variance(),
            'fairness_rating' => $variance(),
            'workload_rating' => max(1, min(5, $baseRating + fake()->numberBetween(-2, 0))),
            'comment' => fake()->randomElement(self::$comments),
            'status' => 'published',
        ];
    }
}
