<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Entities\Lesson;

class SendLessonNotEndNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Lesson $lesson)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toOneSignal($notifiable)
    {
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Lesson Not End');
        OneSignalFacade::sendNotificationToUser(
            "Hey, " . $notifiable->first_name . ". This is a gentle reminder that you haven't end your lesson yet.",
            $notifiable->player_id,
        );
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent ',
            $notifiable->player_id, "Hey, " . $notifiable->first_name . ". This is a gentle reminder that you haven't end your lesson yet.",'Lesson Not End');
    }
}
