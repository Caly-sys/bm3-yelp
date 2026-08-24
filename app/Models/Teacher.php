<?php

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'photo',
    ];

    /**
     * Reviews for this teacher.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average of a specific rating column.
     */
    public function averageRating(string $column = 'overall_rating'): float
    {
        return round((float) $this->reviews()
            ->where('status', 'published')
            ->avg($column), 1);
    }

    /**
     * Get all average ratings as an array.
     */
    public function allAverageRatings(): array
    {
        $query = $this->reviews()->where('status', 'published');

        return [
            'overall' => round((float) (clone $query)->avg('overall_rating'), 1),
            'teaching' => round((float) (clone $query)->avg('teaching_rating'), 1),
            'explanation' => round((float) (clone $query)->avg('explanation_rating'), 1),
            'fairness' => round((float) (clone $query)->avg('fairness_rating'), 1),
            'workload' => round((float) (clone $query)->avg('workload_rating'), 1),
        ];
    }

    /**
     * Get the number of published reviews.
     */
    public function publishedReviewCount(): int
    {
        return $this->reviews()->where('status', 'published')->count();
    }

    /**
     * Get the initials for a default avatar.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials;
    }
}
