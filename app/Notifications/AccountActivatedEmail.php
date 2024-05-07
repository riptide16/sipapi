<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var EmailTemplate
     */
    public $emailTemplate;

    public function __construct()
    {
        $this->emailTemplate = EmailTemplate::where('slug', 'notifikasi-akun-aktif')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->emailTemplate->subject)
            ->line($this->emailTemplate->body)
            ->action($this->emailTemplate->action_button, $this->loginUrl());
    }

    protected function loginUrl()
    {
        return config('services.frontend.login_url');
    }
}
