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
use Modules\Drivisa\Entities\Instructor;
use Carbon\Carbon;

class TraineeRoadTestRequestNotification extends Notification
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
        $instructor_name = $this->instructor->full_name;

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->rentalRequest,'notification ready to send','-','','Rental Reschedule Request');
        if ($this->rentalRequest->is_reschedule_request == 1) {
            OneSignalFacade::sendNotificationToUser(
                "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent to $instructor_name",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                $notifiable->player_id,    "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent to $instructor_name",
                'Rental Reschedule Request');
        } else {
            OneSignalFacade::sendNotificationToUser(
                "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent to $instructor_name",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->rentalRequest, 'notification sent to trainee',
                $notifiable->player_id,
                "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent to $instructor_name",
                'Rental Reschedule Request');
        }
    }

    public function toArray($notifiable)
    {
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $instructor_name = $this->instructor->full_name;

        if ($this->rentalRequest->is_reschedule_request == 1) {
            return [
                'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Reschedule Request sent to $instructor_name",
            ];
        } else {
            return [
                'message' => "$rental_request_type at $rental_request_date $rental_request_time Rental Request sent to $instructor_name",
            ];
        }
    }
}
