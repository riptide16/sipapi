<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApproveNewlyVerifiedUser extends Notification
{
    use Queueable;

    /**
     * @var App\Models\User
     */
    protected $verifiedUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $verifiedUser)
    {
        $this->verifiedUser = $verifiedUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Approve user baru',
            'body' => "Akun {$this->verifiedUser->name} baru saja terverifikasi. Mohon di-approve.",
            'target_url' => config('services.frontend.admin_url') . "/users/edit/{$this->verifiedUser->id}",
            'user_id' => $this->verifiedUser->id,
        ];
    }
}
