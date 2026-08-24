<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'user_id',
        'overall_rating',
        'teaching_rating',
        'explanation_rating',
        'fairness_rating',
        'workload_rating',
        'comment',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'teaching_rating' => 'integer',
            'explanation_rating' => 'integer',
            'fairness_rating' => 'integer',
            'workload_rating' => 'integer',
        ];
    }

    /**
     * The user who wrote this review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The teacher this review is about.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Helpful votes on this review.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Reports on this review.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the number of helpful votes.
     */
    public function helpfulCount(): int
    {
        return $this->votes()->count();
    }

    /**
     * Check if a specific user has voted on this review.
     */
    public function hasVoteFrom(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }
}
