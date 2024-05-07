<?php

namespace App\Notifications;

use App\Models\InstitutionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ValidateAssesseeInstitutionSubmission extends Notification
{
    use Queueable;

    protected $institution;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InstitutionRequest $institution)
    {
        $this->institution = $institution;
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
            'institution_request_id' => $this->institution->id,
            'title' => 'Mohon Validasi Pengisian Data Kelembagaan',
            'body' => "Data kelembagaan {$this->institution->library_name} telah diisi. Mohon divalidasi.",
            'target_url' => config('services.frontend.admin_url') . "/data-kelembagaan/verifikasi/{$this->institution->id}",
        ];
    }
}
