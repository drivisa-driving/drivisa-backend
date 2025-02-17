<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Events\ChangeUserPassword;

class ChangeUserPasswordEmail implements ShouldQueue
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

    public function __construct(Mailer $mail)
    {
        $this->mail = $mail;
    }

    public function handle(ChangeUserPassword $event)
    {
        $user = $event->user;
        $password = $event->password;

        $this->sendViaPostmark($user, $password);
    }

    public function sendViaPostmark($user, $password)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.change_user_password'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'password' => $password
                    ])
            );
    }
}
