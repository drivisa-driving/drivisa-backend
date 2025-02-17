<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Modules\User\Contracts\Authentication;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\CancelLessonByInstructor;

class NotifyTraineeInstructorLessonCancellation implements ShouldQueue
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
    public function handle(CancelLessonByInstructor $event)
    {
        $lesson = $event->lesson;
        $this->sendViaPostmark($lesson);
    }


    public function sendViaPostmark($lesson)
    {
        $this->mail->to($lesson->trainee->user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.instructor_cancel_lesson_trainee_notify'))
                    ->include([
                        'name' => $lesson->trainee->user->first_name . " " . $lesson->trainee->user->last_name,
                        'instructor_name' => $lesson->instructor->first_name . " " . $lesson->instructor->last_name,
                        'product_name' => 'Drivisa',
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType()
                    ])
            );
    }
}
