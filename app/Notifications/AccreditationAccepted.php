<?php

namespace App\Notifications;

use App\Models\Accreditation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccreditationAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accreditation;

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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'accreditation_id' => $this->accreditation->id,
            'title' => "Hasil Akreditasi Diterima - {$this->accreditation->institution->library_name}",
            'body' => 'Hasil Akreditasi Diterima',
            'target_url' => config('services.frontend.admin_url') . "/akreditasi/" . $this->accreditation->id,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->accreditation->code} - Pengajuan Akreditasi Diterima")
            ->line("{$this->accreditation->code} - Pengajuan Akreditasi Diterima");
    }
}
