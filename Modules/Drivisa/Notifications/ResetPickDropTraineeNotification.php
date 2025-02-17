<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Drivisa\Entities\Lesson;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;

class ResetPickDropTraineeNotification extends Notification
{
    use Queueable;

    public Lesson $lesson;

    public $difference;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Lesson $lesson, $difference)
    {
        $this->lesson = $lesson;
        $this->difference = $difference;
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
        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Pick-up and Drop-off location update');
        OneSignalFacade::sendNotificationToUser($this->message(), $notifiable->player_id);
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee ',
            $notifiable->player_id, $this->message(),'Pick-up and Drop-off location update');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message(),
        ];
    }

    private function message()
    {
        $pickPoint = json_decode(stripslashes($this->lesson->pickup_point),true);
        $address = data_get($pickPoint, 'address');
        $trainingDate = Carbon::parse($this->lesson->start_at)->format('M d Y');
        $trainingTime = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lessonType = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lessonType = $lessonType === 'Driving' ? "Lesson" : strtoupper($lessonType);

        $message = "Pick-up and Drop-off location for $lessonType at $trainingDate $trainingTime has been updated. The updated Pick-up and Drop-off location is $address.";

        if ($this->difference > 0) {
            $message .= ". Refund amount has been initiated for additional km.";
        }

        return $message;
    }
}
