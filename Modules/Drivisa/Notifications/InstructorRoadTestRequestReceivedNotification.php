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
use Modules\Drivisa\Entities\Lesson;
use Carbon\Carbon;

class InstructorRoadTestRequestReceivedNotification extends Notification
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
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $trainee_name = $this->rentalRequest->trainee->full_name;

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Rental Reschedule Request');
        if ($this->rentalRequest->is_reschedule_request == 1) {
            OneSignalFacade::sendNotificationToUser(
                "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent by $trainee_name",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to instructor',
                $notifiable->player_id, "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent by $trainee_name",
                'Rental Reschedule Request');
        } else {
            OneSignalFacade::sendNotificationToUser(
                "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent by $trainee_name",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to instructor',
                $notifiable->player_id, "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent by $trainee_name",
                'Rental Reschedule Request');
        }
    }

    public function toArray($notifiable)
    {
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $trainee_name = $this->rentalRequest->trainee->full_name;
        $avatar = $this->rentalRequest->trainee->user->present()->gravatar();

        if ($this->rentalRequest->is_reschedule_request == 1) {
            return [
                'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent by $trainee_name",
                'avatar' => $avatar
            ];
        } else {
            return [
                'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent by $trainee_name",
                'avatar' => $avatar
            ];
        }
    }
}
