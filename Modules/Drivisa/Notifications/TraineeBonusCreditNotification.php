<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;

class TraineeBonusCreditNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(public $trainee, public $data)
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
        $type = $this->data['type'];
        $credit = $this->data['credit'];

        if ($type == "Bonus_BDE") {
            $credit_type = "BDE";
        } else if ($type == "Bonus") {
            $credit_type = "Lesson";
        }

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(2,'notification ready to send','-','','Bonus '.$credit_type.' has been credited',$this->trainee->id);
        OneSignalFacade::sendNotificationToUser(
            "Hey! $trainee_name, $credit hour's of Bonus $credit_type has been credited to your account",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(2,'notification sent to Trainee',
            $notifiable->player_id,
            "Hey! $trainee_name, $credit hour's of Bonus $credit_type has been credited to your account",'Bonus $credit_type has been credited',$this->trainee->id);
    }

    public function toArray($notifiable)
    {
        $trainee_name = $this->trainee->full_name;
        $type = $this->data['type'];
        $credit = $this->data['credit'];

        if ($type == "Bonus_BDE") {
            $credit_type = "BDE";
        } else if ($type == "Bonus") {
            $credit_type = "Lesson";
        }

        return [
            "Hey! $trainee_name, $credit hour's of Bonus $credit_type has been credited to your account"
        ];
    }
}
