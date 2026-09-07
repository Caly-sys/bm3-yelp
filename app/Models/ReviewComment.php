<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'content',
    ];

    /**
     * The review this comment belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * The user who wrote this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
