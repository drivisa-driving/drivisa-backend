<?php

namespace Modules\Pusher\Notifications;

use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Services\OneSignalNotificationService;

class ChatNotificationToUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $data, public $lesson, public $sender)
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
        $training_end_time = Carbon::parse($this->lesson->end_at)->format('h:i A');
        $senderFullName = $this->sender->first_name . ' ' . $this->sender->last_name;

        OneSignalFacade::sendNotificationToUser(
            'You received a message from ' . $senderFullName,
            $notifiable->player_id,
            null,
            [
                'lesson_id' => $this->lesson->id,
                'lesson_date' => $training_date,
                'lesson_start_time' => $training_time,
                'lesson_end_time' => $training_end_time,
                'lesson_type' => array_search($this->lesson->lesson_type, Lesson::TYPE),
                'sender_avatar' => $this->sender->present()->gravatar(),
                'sender_name' => $senderFullName
            ]
        );
    }

    public function toArray($notifiable)
    {

        $training_date = Carbon::parse($this->lesson->start_at)->format('M d Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $training_end_time = Carbon::parse($this->lesson->end_at)->format('h:i A');
        $senderFullName = $this->sender->first_name . ' ' . $this->sender->last_name;

        return [
            'message' => 'You received a message from ' . $senderFullName,
            'lesson_id' => $this->lesson->id,
            'lesson_date' => $training_date,
            'lesson_start_time' => $training_time,
            'lesson_end_time' => $training_end_time,
            'lesson_type' => array_search($this->lesson->lesson_type, Lesson::TYPE),
            'sender_avatar' => $this->sender->present()->gravatar(),
            'sender_name' => $senderFullName
        ];
    }
}
