<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\Drivisa\Emails\SendTraineeRejectionMail;
use Modules\Drivisa\Events\TraineeAccountRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Entities\Sentinel\User;

class NotifyTraineeOfAccountRejection implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'listeners';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 20;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TraineeAccountRejected $event
     * @return void
     */
    public function handle(TraineeAccountRejected $event)
    {
        $user = User::find($event->trainee->user_id);
        Mail::to($user->email)->send(new SendTraineeRejectionMail($event->trainee, $event->message));
    }
}
