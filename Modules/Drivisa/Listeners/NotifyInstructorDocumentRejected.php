<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\InstructorDocumentRejected;

class NotifyInstructorDocumentRejected implements ShouldQueue
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
    public function handle(InstructorDocumentRejected $event)
    {
        $this->sendViaPostmark($event->instructor->user, $event->document_name, $event->reason);
    }

    public function sendViaPostmark($user, $document_name, $reason)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.instructor_document_rejected'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'document_name' => $document_name,
                        'reason' => $reason,
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'action_url' => env('APP_LIVE_URL') . "/instructor/documents"
                    ])
            );
    }

    public function sendMailViaSendgrid($user, $document_name, $reason)
    {
        $sendGrid = new SendGridMailable();

        $data = [
            'name' => $user->first_name . " " . $user->last_name,
            'reason' => $reason,
            'document_name' => $document_name,
            'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
            'action_url' => env('APP_LIVE_URL') . "/instructor/documents"
        ];

        $sendGrid->sendMail(
            config('template.sendgrid.instructor_document_rejected'),
            $user->email,
            $user->first_name . " " . $user->last_name,
            'Instructor Document Rejected',
            $data
        );
    }
}
