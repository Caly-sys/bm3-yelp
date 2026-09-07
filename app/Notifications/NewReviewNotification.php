<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Review $review,
        public User $reviewer,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'teacher_id' => $this->review->teacher_id,
            'reviewer_username' => $this->reviewer->username,
            'message' => "You received a new review from @{$this->reviewer->username}",
            'link' => route('teachers.show', $this->review->teacher_id),
        ];
    }
}
