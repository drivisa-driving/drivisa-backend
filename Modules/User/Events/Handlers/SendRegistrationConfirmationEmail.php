<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Emails\WelcomeEmail;
use Modules\User\Contracts\Authentication;
use Modules\User\Events\UserHasRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Services\DynamicUrlService;

class SendRegistrationConfirmationEmail implements ShouldQueue
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
    /**
     * @var DynamicUrlService
     */
    private $dynamicUrlService;

    public function __construct(Authentication $auth, Mailer $mail, DynamicUrlService $dynamicUrlService)
    {
        $this->auth = $auth;
        $this->mail = $mail;
        $this->dynamicUrlService = $dynamicUrlService;
    }

    public function handle(UserHasRegistered $event)
    {
        $user = $event->user;

        $activationCode = $this->auth->createActivation($user);

        //        $this->sendMailViaSendgrid($user, $activationCode);
        $this->sendViaPostmark($user, $activationCode);
    }

    public function sendMailViaSMTP($user, $activationCode)
    {
        $this->mail->to($user->email)->send(new WelcomeEmail($user, $activationCode));
    }

    public function sendViaPostmark($user, $activationCode)
    {
        $url = env('APP_LIVE_URL')
            . '/activate-account?username=' . $user->username . '&code=' . $activationCode;

        $dynamicUrl = $this->dynamicUrlService->dynamicUrl($url);

        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.account_confirmation'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'otp' => $activationCode,
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        "action_url" => $dynamicUrl,
                    ])
            );
    }

    public function sendMailViaSendgrid($user, $activationCode)
    {
        $sendGrid = new SendGridMailable();

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'otp' => $activationCode,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            "action_url" => env('APP_LIVE_URL')
                . '/activate-account?username=' . $user->username . '&code=' . $activationCode,
        ];

        $sendGrid->sendMail(
            config('template.sendgrid.account_confirmation'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Account Confirmation',
            $data
        );
    }
}
