<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $newUser,
        public User $createdBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $roleName = match($this->newUser->role) {
            'admin' => 'Admin',
            'teacher' => 'Teacher',
            default => 'Siswa',
        };

        return [
            'type' => 'account_created',
            'message' => "👤 New {$roleName} account created: @{$this->newUser->username} by @{$this->createdBy->username}",
            'link' => route('admin.users.index'),
            'new_user_id' => $this->newUser->id,
            'new_username' => $this->newUser->username,
            'created_by_username' => $this->createdBy->username,
            'role' => $this->newUser->role,
        ];
    }
}
