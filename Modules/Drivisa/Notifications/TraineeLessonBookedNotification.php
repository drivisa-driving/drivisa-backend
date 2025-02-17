<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Berkayk\OneSignal\OneSignalFacade;
use App\Notifications\OneSignalChannel;
use Illuminate\Support\Facades\Log;
use Modules\Drivisa\Entities\Lesson;
use Carbon\Carbon;
use Modules\Drivisa\Services\OneSignalNotificationService;

class TraineeLessonBookedNotification extends Notification
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
        $date = Carbon::parse($this->lesson->start_at)->subMinutes(30)->format('M d Y h:i:s A') . " UTC-04:00";

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Lesson booked');
        OneSignalFacade::sendNotificationToUser(
            "Lesson booked successfully",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
            $notifiable->player_id,   "Lesson booked successfully",'Lesson booked');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Lesson booked successfully",
        ];
    }
}
