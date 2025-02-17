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

class SendLessonIsAboutToStartNotification extends Notification
{
    use Queueable;

    public $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Lesson $lesson, $type)
    {
        $this->lesson = $lesson;
        $this->type = $type;
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
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send'.$this->type,'-','-','30 minutes notification');
        OneSignalFacade::sendNotificationToUser(
            "Less Than 30 minutes remaining to start your lesson",
            $notifiable->player_id,
        );
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to '.$this->type,
            $notifiable->player_id,"Less Than 30 minutes remaining to start your lesson",'30 minutes notification');

    }
}
