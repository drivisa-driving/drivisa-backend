<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Modules\Drivisa\Events\SendMessage;

class SendMessageToUser
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


    private $mail;


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
    public function handle(SendMessage $event)
    {
        $user = $event->trainee->user;
        $message = $event->message;

        $this->sendViaPostmark($user, $message);
    }

    public function sendViaPostmark($user, $message)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.send_message_to_user'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'product_name' => 'Drivisa',
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'message' => $message,
                    ])
            );
    }
}
