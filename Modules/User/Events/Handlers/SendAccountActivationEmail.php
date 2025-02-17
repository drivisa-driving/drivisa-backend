<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Services\SendGridMailable;
use Modules\User\Contracts\Authentication;
use Modules\User\Emails\WelcomeEmail;
use Modules\User\Events\AccountActivated;
use Modules\User\Events\UserHasRegistered;

class SendAccountActivationEmail implements ShouldQueue
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
     * @var Authentication
     */
    private $auth;
    /**
     * @var Mailer
     */
    private $mail;

    public function __construct(Authentication $auth, Mailer $mail)
    {
        $this->auth = $auth;
        $this->mail = $mail;
    }

    public function handle(AccountActivated $event)
    {
        $user = $event->user;

//        $this->sendViaSendGrid($user);
        $this->sendViaPostmark($user);
    }

    public function sendViaPostmark($user)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.welcome_mail'))
                ->include([
                    'name' => $user->first_name . " " . $user->last_name,
                    'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                    "action_url" => env('APP_LIVE_URL') . '/login',
                ])
            );
    }

    public function sendViaSendGrid($user)
    {
        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            "action_url" => env('APP_LIVE_URL')
                . '/login',
        ];

        $sendGrid = new SendGridMailable();

        $sendGrid->sendMail(
            config('template.sendgrid.welcome_mail'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Account Confirmation',
            $data
        );
    }
}
