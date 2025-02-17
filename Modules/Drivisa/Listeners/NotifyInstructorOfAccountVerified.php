<?php

namespace Modules\Drivisa\Listeners;


use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Emails\SendInstructorVerificationMail;
use Modules\Drivisa\Events\InstructorAccountVerified;
use Modules\User\Entities\Sentinel\User;

class NotifyInstructorOfAccountVerified implements ShouldQueue
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

    public $mail;

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
    public function handle(InstructorAccountVerified $event)
    {
        $user = $event->instructor->user;

        //  $this->sendMailViaSendgrid($user);
        $this->sendViaPostmark($user);
    }

    public function mailViaSMTP($event)
    {
        $user = User::find($event->instructor->user_id);
        Mail::to($user->email)->send(new SendInstructorVerificationMail($event->instructor, $event->message));
    }

    public function sendViaPostmark($user)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.instructor_account_approved'))
                ->include([
                    'name' => $user->first_name . " " . $user->last_name,
                    'action_url' => env('APP_LIVE_URL') . "/login",
                    'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                ])
            );
    }

    public function sendMailViaSendgrid($user)
    {
        $sendGrid = new SendGridMailable();

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            'action_url' => env('APP_LIVE_URL') . "/login",
        ];

        $sendGrid->sendMail(
            config('template.sendgrid.instructor_account_approved'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Instructor Account Verified',
            $data
        );
    }
}
