<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Accreditation;

class CertificationSigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $accreditation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Accreditation $accreditation)
    {
        $this->accreditation = $accreditation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the Database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'accreditation_id' => $this->accreditation->id,
            'title' => "Sertifikat Sedang Ditandatangani - {$this->accreditation->code}",
            'body' => "Sertifikat dengan no. {$this->accreditation->code} sedang proses penandatanganan",
            'target_url' => config('services.frontend.admin_url') . "/akreditasi/",
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->accreditation->code} - Sertifikasi Anda Sedang Ditandatangani")
            ->line("{$this->accreditation->code} - Sertifikasi Anda Sedang Ditandatangani.");
    }
}
