<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Indonesian-style teacher names and subjects for BM3.
     */
    private static array $subjects = [
        'Matematika',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Fisika',
        'Kimia',
        'Biologi',
        'Pemrograman Web',
        'Desain Grafis',
        'Basis Data',
        'Jaringan Komputer',
        'Rekayasa Perangkat Lunak',
        'Multimedia',
        'Animasi',
        'Kewirausahaan',
        'Pendidikan Agama',
        'PKN',
        'Sejarah',
        'Seni Budaya',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $prefix = $gender === 'male' ? 'Pak' : 'Bu';
        $name = $prefix . ' ' . fake('id_ID')->firstName($gender) . ' ' . fake('id_ID')->lastName();

        return [
            'name' => $name,
            'subject' => fake()->randomElement(self::$subjects),
            'description' => fake()->paragraph(3),
            'photo' => null,
        ];
    }
}
