<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Drivisa\Emails\SendDailyActivityMailAdmin;
use Modules\Drivisa\Events\DailyActivityMail;
use Modules\Drivisa\Transformers\LessonTransformer;

class NotifyAdminAboutDailyActivity implements ShouldQueue
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
     * @var Mailer
     */
    private $mail;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(DailyActivityMail $event)
    {
        $this->sendMailViaSMTP($event);
    }

    public function sendMailViaSMTP($event)
    {
        $admin_email = env("ADMIN_EMAIL");
        $lessons = LessonTransformer::collection($event->data);
        Mail::to($admin_email)->send(new SendDailyActivityMailAdmin($lessons, $event->lessons_count));
    }
}
