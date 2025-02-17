<?php

namespace Modules\Drivisa\Listeners;

use Modules\Drivisa\Events\DocumentUploadedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;

class NotifyAdminDocumentUploadedListener implements ShouldQueue
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
    public function handle(DocumentUploadedEvent $event)
    {
        $user = $event->user;
        $instructor = $event->instructor;

        $this->sendViaPostmark($user, $instructor);
    }



    public function sendViaPostmark($user, $instructor)
    {
        $admin_email = env("ADMIN_EMAIL");

        $this->mail->to($admin_email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.instructor_document_upload'))
                    ->include([
                        'url' => config('app.url'),
                        'logo' => config('app.url') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'subject' => 'New Document Verification Request Received',
                        "instructor" => $user->first_name . " " . $user->last_name,
                        'instructor_profile_url' => config('app.url') . "/admin/instructors/details/" . $instructor->id,
                    ])
            );
    }
}
