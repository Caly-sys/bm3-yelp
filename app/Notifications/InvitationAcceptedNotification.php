<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvitationAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invitation $invitation,
        public User $acceptedUser,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $roleName = $this->invitation->role === 'teacher' ? 'Teacher' : 'Siswa';

        return [
            'invitation_id' => $this->invitation->id,
            'type' => 'invitation_accepted',
            'message' => "✅ @{$this->acceptedUser->username} accepted your {$roleName} invitation",
            'link' => route('profile.show'),
            'accepted_username' => $this->acceptedUser->username,
            'role' => $this->invitation->role,
        ];
    }
}
