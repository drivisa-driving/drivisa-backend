<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Modules\Drivisa\Entities\Lesson;
use Berkayk\OneSignal\OneSignalFacade;
use App\Notifications\OneSignalChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Drivisa\Entities\RentalRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Services\OneSignalNotificationService;

class RoadTestRequestPaidNotification extends Notification
{
    use Queueable;

    public RentalRequest $rentalRequest;
    public Lesson $lesson;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RentalRequest $rentalRequest, Lesson $lesson)
    {
        $this->rentalRequest = $rentalRequest;
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

        $date = $this->rentalRequest->booking_date->toDateString();
        $time = $this->rentalRequest->booking_time->toTimeString();
        $dateTime = Carbon::parse($date . $time)->subMinutes(30)->format('M d Y h:i:s A') . " UTC-04:00";

        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $trainee_name = $this->rentalRequest->trainee->full_name;

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Road Test Paid');
        OneSignalFacade::sendNotificationToUser(
            "$rental_request_type at $rental_request_date $rental_request_time paid by $trainee_name",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
            $notifiable->player_id, "$rental_request_type at $rental_request_date $rental_request_time paid by $trainee_name",'Road Test Paid');
    }

    public function toArray($notifiable)
    {
        $rental_request_date = Carbon::parse($this->rentalRequest->booking_date)->format('M d Y');
        $rental_request_time = Carbon::parse($this->rentalRequest->booking_time)->format('h:i A');
        $rental_request_type = $this->rentalRequest->type == 1 ? "G Test" : "G2 Test";
        $trainee_name = $this->rentalRequest->trainee->full_name;
        $avatar = $this->rentalRequest->trainee->user->present()->gravatar();

        return [
            'message' => "$rental_request_type at $rental_request_date $rental_request_time paid by $trainee_name",
            'avatar' => $avatar
        ];
    }
}
