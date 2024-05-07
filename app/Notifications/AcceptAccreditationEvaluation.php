<?php

namespace App\Notifications;

use App\Models\Accreditation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptAccreditationEvaluation extends Notification implements ShouldQueue
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
            'title' => 'Akreditasi Telah Diberikan',
            'body' => "Akreditasi lembaga Anda telah diberikan dan mendapatkan predikat {$this->accreditation->predicate}",
            'target_url' => config('services.frontend.admin_url') . "/akreditasi/accept/{$this->accreditation->id}",
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->accreditation->code} - Akreditasi Telah Diberikan")
            ->line("Akreditasi lembaga Anda telah diberikan dan mendapatkan predikat {$this->accreditation->predicate}.");
    }
}
