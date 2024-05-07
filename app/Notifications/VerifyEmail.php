<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\UserVerificationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;

class VerifyEmail extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * @var EmailTemplate
     */
    public $emailTemplate;

    public function __construct()
    {
        $this->emailTemplate = EmailTemplate::where('slug', 'verifikasi-email')->first();
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return config('services.frontend.verification_url')
            . '/'
            . $this->verificationToken($notifiable);
    }

    /**
     * Create a verification token and return it.
     *
     * @return string
     */
    protected function verificationToken($notifiable)
    {
        $expires = now()->addMinutes(config('auth.verification.expire'));
        return UserVerificationToken::create([
            'user_id' => $notifiable->id,
            'expires_at' => $expires,
        ])->token;
    }

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject($this->emailTemplate->subject)
            ->line($this->emailTemplate->body)
            ->action($this->emailTemplate->action_button, $url);
    }
}
