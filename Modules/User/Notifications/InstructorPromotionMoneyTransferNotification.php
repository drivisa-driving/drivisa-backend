<?php

namespace Modules\User\Notifications;

use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InstructorPromotionMoneyTransferNotification extends Notification
{
    use Queueable;

    public $amount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($amount)
    {
        //
        $this->amount = $amount;
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

        OneSignalFacade::sendNotificationToUser(
            "Cheers! You received promotion benefit of \$$this->amount from Drivisa!",
            $notifiable->player_id);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Cheers! You received promotion benefit of \$$this->amount from Drivisa!",
        ];
    }
}
