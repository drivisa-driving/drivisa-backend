<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Mail\Mailer;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\InstructorAccountRejected;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyInstructorOfAccountRejection implements ShouldQueue
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


    public function __construct(
        public Mailer $mail
    )
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(InstructorAccountRejected $event)
    {
        $user = $event->instructor->user;
        $this->sendViaPostmark($user, $event->message);
    }

    public function sendViaPostmark($user, $reason)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.instructor_account_rejected'))
                ->include([
                    'name' => $user->first_name . " " . $user->last_name,
                    'reason' => $reason,
                    'action_url' => env('APP_LIVE_URL') . "/login",
                    'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                ])
            );
    }

    public function sendMailViaSendgrid($user, $reason)
    {
        $sendGrid = new SendGridMailable();

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'reason' => $reason,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            'action_url' => env('APP_LIVE_URL') . "/login",
        ];

        $sendGrid->sendMail(
            config('template.sendgrid.instructor_account_rejected'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Instructor Account Rejected',
            $data
        );
    }
}
