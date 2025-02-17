<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;

class TraineeCreditReduceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(public $trainee, public $course_available, public $data)
    {
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
        $trainee_name = $this->trainee->full_name;
        $credit_reduce = $this->data['credit_reduce'];
        $reason = $this->data['note'];

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(2,'notification ready to send','-','', "hour's  reduced",$this->trainee->id);
        OneSignalFacade::sendNotificationToUser(
            "Hey! $trainee_name, $credit_reduce hour's has been reduced from your account. Refund will be credited to your account. Reason: $reason",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(2,'notification sent ',
            $notifiable->player_id,
            "Hey! $trainee_name, $credit_reduce hour's has been reduced from your account. Refund will be credited to your account. Reason: $reason",
            "hour's  reduced",$this->trainee->id);
    }

    public function toArray($notifiable)
    {
        $trainee_name = $this->trainee->full_name;
        $credit_reduce = $this->data['credit_reduce'];
        $reason = $this->data['note'];

        return [
            "Hey! $trainee_name, $credit_reduce hour's has been reduced from your account. Refund will be credited to your account. Reason: $reason"
        ];
    }
}
