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

class RentalRequestChatNotificationToUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $data, public $rentalRequest, public $sender)
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
        $training_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $training_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $senderFullName = $this->sender->first_name . ' ' . $this->sender->last_name;

        OneSignalFacade::sendNotificationToUser(
            'You received a message from ' . $senderFullName,
            $notifiable->player_id,
            null,
            [
                'request_id' => $this->rentalRequest->id,
                'request_date' => $training_date,
                'request_time' => $training_time,
                'sender_avatar' => $this->sender->present()->gravatar(),
                'sender_name' => $senderFullName
            ]
        );
    }

    public function toArray($notifiable)
    {
        $training_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $training_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $senderFullName = $this->sender->first_name . ' ' . $this->sender->last_name;

        return [
            'message' => 'You received a message from ' . $senderFullName,
            'request_id' => $this->rentalRequest->id,
            'request_date' => $training_date,
            'request_time' => $training_time,
            'sender_avatar' => $this->sender->present()->gravatar(),
            'sender_name' => $senderFullName
        ];
    }
}
