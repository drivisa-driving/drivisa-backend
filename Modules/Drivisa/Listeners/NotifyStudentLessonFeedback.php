<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\NewLessonBooked;
use Modules\User\Contracts\Authentication;


class NotifyStudentLessonFeedback implements ShouldQueue
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
    public function handle(NewLessonBooked $event)
    {

        $user = $event->trainee->user;
        $document_name = $event->document_name;
        $lesson = $event->lesson;
        $instructor = $event->instructor;
        $this->sendViaPostmark($user, $document_name, $lesson, $instructor);
    }

    public function sendViaPostmark($user, $document_name, $reason, $instructor)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.student_lesson_feedback'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'product_name' => 'Drivisa',
                        'instructor_name' => $instructor->first_name . " " . $instructor->last_name,
                        'action_url' => 'action_url',
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                    ])
            );
    }
}
