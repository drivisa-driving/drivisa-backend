<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\User\Emails\ResetPasswordEmail;
use Modules\Drivisa\Services\DynamicUrlService;
use Modules\User\Events\UserHasBegunResetProcess;

class SendResetCodeEmail implements ShouldQueue
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
    private $mailer;

    /**
     * @var DynamicUrlService
     */
    private $dynamicUrlService;

    public function __construct(Mailer $mailer, DynamicUrlService $dynamicUrlService)
    {
        $this->mailer = $mailer;
        $this->dynamicUrlService = $dynamicUrlService;
    }

    public function handle(UserHasBegunResetProcess $event)
    {
        //        $this->sendViaSendGrid($event->user, $event->code);
        $this->sendViaPostmark($event->user, $event->code);
    }

    public function sendViaSMTP($user, $code)
    {
        $this->mailer->to($user->email)->send(new ResetPasswordEmail($user, $code));
    }

    public function sendViaPostmark($user, $code)
    {

        $url = env('APP_LIVE_URL') . "/complete-reset-password?username={$user['username']}&code={$code}";

        $dynamicUrl = $this->dynamicUrlService->dynamicUrl($url);

        $this->mailer->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.password_reset'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        "action_url" => $dynamicUrl,
                        "resetpassword_url" => $dynamicUrl,
                        "code" => $code
                    ])
            );
    }

    public function sendViaSendGrid($user, $code)
    {
        $url = env('APP_LIVE_URL') . "/complete-reset-password?username={$user['username']}&code={$code}";

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            "action_url" => $url,
            "resetpassword_url" => $url,
        ];

        $sendGrid = new SendGridMailable();

        $sendGrid->sendMail(
            config('template.sendgrid.password_reset'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Account Confirmation',
            $data
        );
    }
}
