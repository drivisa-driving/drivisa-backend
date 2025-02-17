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
use Modules\Drivisa\Entities\Instructor;

class TraineeRoadTestRequestReceivedNotification extends Notification
{
    use Queueable;

    public RentalRequest $rentalRequest;
    public Instructor $instructor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RentalRequest $rentalRequest, Instructor $instructor)
    {
        $this->rentalRequest = $rentalRequest;
        $this->instructor = $instructor;
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
        $instructor_name = $this->rentalRequest->acceptedBy?->full_name;

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Reschedule Request is Declined');
        if ($this->rentalRequest->status === 5) {
            if ($this->rentalRequest->is_reschedule_request == 1) {
                OneSignalFacade::sendNotificationToUser(
                    "Your Rental Reschedule Request is Declined by $instructor_name",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id,    "Your Rental Reschedule Request is Declined by $instructor_name",
                    'Reschedule Request is Declined');
            } else {
                OneSignalFacade::sendNotificationToUser(
                    "Your Rental Request is Declined  by $instructor_name",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id,    "Your Rental Request is Declined  by $instructor_name",
                    'Reschedule Request is Declined');
            }
        } else {
            NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Reschedule Request is Accepted');
            if ($this->rentalRequest->is_reschedule_request == 1) {
                OneSignalFacade::sendNotificationToUser(
                    "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request accepted by $instructor_name .",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id,    "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request accepted by $instructor_name .",
                    'Reschedule Request is Accepted');
            } else {
                OneSignalFacade::sendNotificationToUser(
                    "$rental_request_type at $rental_request_date $rental_request_time Rental Request accepted by $instructor_name . You have 24 hours to complete the payment before the request expires",
                    $notifiable->player_id
                );
                NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                    $notifiable->player_id,     "$rental_request_type at $rental_request_date $rental_request_time Rental Request accepted by $instructor_name . You have 24 hours to complete the payment before the request expires",
                    'Reschedule Request is Accepted');
            }
        }
    }

    public function toArray($notifiable)
    {
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $instructor_name = $this->rentalRequest->acceptedBy?->full_name;
        $avatar = $this->instructor->user->present()->gravatar();

        if ($this->rentalRequest->status === 5) {
            if ($this->rentalRequest->is_reschedule_request == 1) {
                return [
                    'message' => "Your Rental Reschedule Request is Declined by $instructor_name",
                    'avatar' => $avatar
                ];
            } else {
                return [
                    'message' => "Your Rental Request is Declined by $instructor_name",
                    'avatar' => $avatar
                ];
            }
        } else {
            if ($this->rentalRequest->is_reschedule_request == 1) {
                return [
                    'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request accepted by $instructor_name .",
                    'avatar' => $avatar
                ];
            } else {
                return [
                    'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Request accepted by $instructor_name . You have 24 hours to complete the payment before the request expires",
                    'avatar' => $avatar
                ];
            }
        }
    }
}
