<?php

namespace App\Notifications;

use App\Models\Accreditation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyNewAccreditation extends Notification
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
            'accreditation_id' => $this->accreditation->id,
            'title' => 'Mohon Verifikasi Pengajuan Akreditasi',
            'body' => "Lembaga {$this->accreditation->institution->library_name} telah mengajukan Akreditasi. Mohon diverifikasi.",
            'target_url' => config('services.frontend.admin_url') . "/akreditasi/verifikasi/{$this->accreditation->id}",
        ];
    }
}
