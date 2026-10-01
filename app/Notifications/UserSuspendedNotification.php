<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserSuspendedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $suspendedUser,
        public bool $isSuspended,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->isSuspended ? 'suspended' : 'unsuspended';
        $emoji = $this->isSuspended ? '🚫' : '🔓';

        return [
            'type' => 'user_suspended',
            'message' => "{$emoji} Your account has been {$action}",
            'link' => route('home'),
            'is_suspended' => $this->isSuspended,
        ];
    }
}
