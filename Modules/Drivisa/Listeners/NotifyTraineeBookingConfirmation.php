<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\NewLessonBooked;
use Modules\Drivisa\Entities\Lesson;
use Carbon\Carbon;

class NotifyTraineeBookingConfirmation implements ShouldQueue
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
        $lesson = $event->lesson;
        $instructor = $event->instructor;

        $this->sendViaPostmark($user, $instructor, $lesson);
    }

    public function sendViaPostmark($user, $instructor, $lesson)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.student_lesson_confirmation'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        'instructor_name' => $instructor->first_name . " " . $instructor->last_name,
                        "product_name" => "Drivisa",
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'lesson_start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'lesson_start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'lesson_end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType()

                    ])
            );
    }
}
