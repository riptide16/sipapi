<?php

namespace App\Notifications;

use App\Models\EvaluationAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvaluateAccreditation extends Notification
{
    use Queueable;

    protected $assignment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EvaluationAssignment $assignment)
    {
        $this->assignment = $assignment;
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
            'evaluation_assignment_id' => $this->assignment->id,
            'title' => 'Mohon Penilaian Akreditasi',
            'body' => "Lembaga {$this->assignment->accreditation->institution->library_name} telah mengajukan akreditasi. Mohon dinilai.",
            'target_url' => config('services.frontend.admin_url') . "/penilaian",
        ];
    }
}
