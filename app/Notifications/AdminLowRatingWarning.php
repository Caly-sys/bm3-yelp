<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminLowRatingWarning extends Notification
{
    use Queueable;

    public function __construct(
        public Review $review,
        public Teacher $teacher,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_low_rating',
            'message' => "🚨 Warning: {$this->teacher->name} received a {$this->review->overall_rating}⭐ review. Current points: {$this->teacher->points}/100",
            'link' => route('teachers.show', $this->teacher->id) . "#review-{$this->review->id}",
        ];
    }
}
