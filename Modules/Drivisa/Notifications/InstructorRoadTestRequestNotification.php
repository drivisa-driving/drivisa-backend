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

class InstructorRoadTestRequestNotification extends Notification
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

        if ($notifiable->player_id == null) return;


        if ($this->rentalRequest->status === 5) {
            if ($this->rentalRequest->is_reschedule_request == 1) {
                NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Rental Reschedule Request Declined');
                OneSignalFacade::sendNotificationToUser(
                    "Rental Reschedule Request Declined successfully",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id, "Rental Reschedule Request Declined successfully",
                    'Rental Reschedule Request Declined');
            } else {
                NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Rental Request Declined');
                OneSignalFacade::sendNotificationToUser(
                    "Rental Request Declined successfully",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id,  "Rental Request Declined successfully",
                    'Rental Request Declined');
            }
        } else {
            NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Rental Request Accepted');
            OneSignalFacade::sendNotificationToUser(
                "$rental_request_type at $rental_request_date $rental_request_time Accepted",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                $notifiable->player_id,  "$rental_request_type at $rental_request_date $rental_request_time Accepted",
                'Rental Request Accepted');
        }
    }

    public function toArray($notifiable)
    {
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";

        if ($this->rentalRequest->status === 5) {
            if ($this->rentalRequest->is_reschedule_request == 1) {
                return [
                    'message' => "Rental Reschedule Request Declined Successfully",
                ];
            } else {
                return [
                    'message' => "Rental Request Declined Successfully",
                ];
            }
        } else {
            return [
                'message' => "$rental_request_type at $rental_request_date $rental_request_time Accepted",
            ];
        }
    }
}
