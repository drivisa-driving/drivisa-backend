<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Drivisa\Entities\Lesson;

class TraineeRefundOnLessonNotification extends Notification
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
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d, Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Refund amount Credited');
        if ($lesson_type == "BDE") {
            OneSignalFacade::sendNotificationToUser(
                "Refund amount for $lesson_type lesson on $training_date at $training_time has been credited to your account",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
                $notifiable->player_id,   "Refund amount for $lesson_type lesson on $training_date at $training_time has been credited to your account",
                'Refund amount Credited');
        } else {
            OneSignalFacade::sendNotificationToUser(
                "Refund amount for $lesson_type on $training_date at $training_time has been credited to your account",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
                $notifiable->player_id,    "Refund amount for $lesson_type on $training_date at $training_time has been credited to your account",'Refund amount Credited');
        }
    }

    public function toArray($notifiable)
    {
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d, Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);


        if ($lesson_type == "BDE") {
            return [
                'message' => "Refund amount for $lesson_type lesson on $training_date at $training_time has been credited to your account"
            ];
        } else {
            return [
                'message' => "Refund amount for $lesson_type on $training_date at $training_time has been credited to your account"
            ];
        }
    }
}
