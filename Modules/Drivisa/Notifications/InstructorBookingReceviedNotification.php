<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Services\OneSignalNotificationService;

class InstructorBookingReceviedNotification extends Notification
{
    use Queueable;

    public Lesson $lesson;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Lesson $lesson)
    {

        $this->lesson = $lesson;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class, 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toOneSignal($notifiable)
    {
        $trainee_name = $this->lesson->trainee->full_name;
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d Y h:i A');

        $date = Carbon::parse($this->lesson->start_at)->subMinutes(30)->format('M d Y h:i:s A') . " UTC-04:00";

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send User','-','-','Booked Lesson');
        OneSignalFacade::sendNotificationToUser(
            "Lesson booked by the $trainee_name at $training_date",
            $notifiable->player_id,
        );
        NotificationLogCreate::dispatch(1,$this->lesson,
            'notification sent to Instructor',
            $notifiable->player_id,
            "Lesson booked by the $trainee_name at $training_date",
            'Booked Lesson');
    }

    public function toArray($notifiable)
    {
        $trainee_name = $this->lesson->trainee->full_name;
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $avatar = $this->lesson->trainee->user->present()->gravatar();

        return [
            'message' => "Lesson booked by the $trainee_name at $training_date $training_time",
            'avatar' => $avatar
        ];
    }
}
