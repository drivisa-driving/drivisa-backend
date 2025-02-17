<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendMessageToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $message)
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
        if ($notifiable->player_id == null) return;

        $first_name = $notifiable->first_name;
        $last_name = $notifiable->last_name;
        $message = $this->message;
        $trainee_id =0;
        $instructor_id =0;
        if($notifiable->user_type==1){
            $instructor_id= $notifiable->id;
        }else{
            $trainee_id= $notifiable->id;
        }
        NotificationLogCreate::dispatch(2,'notification ready to send','-','','Send Message',$trainee_id,$instructor_id);
        OneSignalFacade::sendNotificationToUser(
            "Hey! $first_name $last_name, $message.",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(2,'notification sent to User',
            $notifiable->player_id, "Hey! $first_name $last_name, $message.",'Send Message',$trainee_id,$instructor_id);
    }

    public function toArray($notifiable)
    {
        $first_name = $notifiable->first_name;
        $last_name = $notifiable->last_name;
        $message = $this->message;

        return [
            'message' => "Hey! $first_name $last_name, $message."
        ];
    }
}
