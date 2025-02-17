<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\InstructorDocumentApproved;
use Modules\User\Contracts\Authentication;

class NotifyInstructorDocumentApproved implements ShouldQueue
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
    public function handle(InstructorDocumentApproved $event)
    {
        $user = $event->user->user;
        $document_name = $event->document_name;

        // $this->sendMailViaSendgrid($user,$document_name);
        $this->sendViaPostmark($user, $document_name);
    }

    public function sendViaPostmark($user, $document_name)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.instructor_document_approved'))
                ->include([
                    'name' => $user->first_name . " " . $user->last_name,
                    'document_name' => $document_name,
                    'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                ])
            );
    }

    public function sendMailViaSendgrid($user, $document_name)
    {
        $sendGrid = new SendGridMailable();

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'document_name' => $document_name,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
        ];

        $sendGrid->sendMail(
            config('template.sendgrid.instructor_document_approved'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Instructor Document Approved',
            $data
        );
    }
}
