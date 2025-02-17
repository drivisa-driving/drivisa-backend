<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;
use Modules\Drivisa\Entities\PackageType;

class TraineeCreditBDENotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(public $trainee, public $package, public $packageType)
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
        $trainee_name = $notifiable->trainee->full_name;
        $hours = (int)$this->package->packageData->hours;
        $package_type = $this->packageType->name;


        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(2,'notification ready to send','-','', '$package_type course  added',$notifiable->trainee->id);
        OneSignalFacade::sendNotificationToUser(
            "Hey! $trainee_name, $hours hours in car lessons of $package_type course has been added to your account",
            $notifiable->player_id
        );
        NotificationLogCreate::dispatch(2,'notification sent ',
            $notifiable->player_id,
            "Hey! $trainee_name, $hours hours in car lessons of $package_type course has been added to your account",'$package_type course  added',$notifiable->trainee->id);
    }

    public function toArray($notifiable)
    {
        $trainee_name = $notifiable->trainee->full_name;
        $hours = (int)$this->package->packageData->hours;
        $package_type = $this->packageType->name;

        return [
            'message' => "Hey! $trainee_name, $hours hours in car lessons of $package_type course has been added to your account"
        ];
    }
}
