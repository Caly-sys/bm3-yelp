<?php

namespace App\Notifications;

use App\Models\ReviewComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewCommentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ReviewComment $comment,
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
        $commenter = $this->comment->user;
        $review = $this->comment->review;

        return [
            'comment_id' => $this->comment->id,
            'review_id' => $review->id,
            'teacher_id' => $review->teacher_id,
            'commenter_username' => $commenter->username,
            'message' => "🎓 @{$commenter->username} responded to a review",
            'link' => route('teachers.show', $review->teacher_id) . "#review-{$review->id}",
        ];
    }
}
