<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvitationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invitation $invitation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $inviter = $this->invitation->inviter;
        $roleName = $this->invitation->role === 'teacher' ? 'Teacher' : 'Siswa';

        return [
            'invitation_id' => $this->invitation->id,
            'type' => 'invitation_received',
            'message' => "📩 You have been invited as a {$roleName} by @{$inviter->username}",
            'link' => route('invitations.accept', $this->invitation->token),
            'inviter_username' => $inviter->username,
            'role' => $this->invitation->role,
        ];
    }
}
