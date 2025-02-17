<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Modules\Drivisa\Entities\RentalRequest;
use Carbon\Carbon;

class RoadTestRequestExpiredNotification extends Notification
{
    use Queueable;

    public RentalRequest $rentalRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RentalRequest $rentalRequest)
    {
        $this->rentalRequest = $rentalRequest;
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
        $expired_payment_time = Carbon::parse($this->rentalRequest->expire_payment_time);
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Road Test Expired');
        OneSignalFacade::sendNotificationToUser(
            "$expired_payment_time $rental_request_type is Expired",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
            $notifiable->player_id, "$expired_payment_time $rental_request_type is Expired",'Road Test Expired');
    }

    public function toArray($notifiable)
    {
        $expired_payment_time = Carbon::parse($this->rentalRequest->expire_payment_time);
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        return [
            'message' => "$expired_payment_time $rental_request_type is Expired",
        ];
    }

}
