<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'reason',
        'details',
        'status',
    ];

    /**
     * The review that was reported.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * The user who filed this report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
