<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InstructorTraineeTrainingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $lesson;
    public function __construct($lesson)
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
        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Instructor waiting to start training');
        OneSignalFacade::sendNotificationToUser(
            "Instructor On location and waiting to start training",
            $notifiable->player_id);
        NotificationLogCreate::dispatch(1,$this->lesson,'notification sent to trainee ',
            $notifiable->player_id,
            "Instructor On location and waiting to start training",'Instructor waiting to start training');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Instructor On location and waiting to start training",
        ];
    }
}
