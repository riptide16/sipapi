<?php

namespace App\Notifications;

use App\Models\Institution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssesseeInstitutionValidated extends Notification
{
    use Queueable;

    protected $institution;
    protected $isValid;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Institution $institution)
    {
        $this->institution = $institution;
        $this->isValid = $institution->status == Institution::STATUS_VALID;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'institution_id' => $this->institution->id,
            'is_valid' => $this->isValid,
            'title' => $this->isValid 
                ? 'Data kelembagaan Anda sudah terverifikasi' 
                : 'Data kelembagaan Anda tidak valid',
            'body' => $this->isValid 
                ? 'Data kelembagaan Anda sudah terverifikasi' 
                : 'Data kelembagaan Anda tidak valid. Mohon perbaharui data kelembagaan.',
            'target_url' => config('services.frontend.admin_url') . "/data-kelembagaan",
        ];
    }
}
