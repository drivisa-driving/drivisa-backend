<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Entities\Lesson;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;
use Modules\Drivisa\Services\OneSignalNotificationService;

class TraineeCancelLessonNotification extends Notification
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
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);

        if ($notifiable->player_id == null) return;

        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Trainee Lesson Cancelled');
        OneSignalFacade::sendNotificationToUser(
            "$lesson_type at $training_date $training_time is Cancelled Successfully",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
            $notifiable->player_id,  "$lesson_type at $training_date $training_time is Cancelled Successfully",'Trainee Lesson Cancelled');
    }

    public function toArray($notifiable)
    {
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);

        return [
            'message' => "$lesson_type at $training_date $training_time is Cancelled Successfully",
        ];
    }
}
