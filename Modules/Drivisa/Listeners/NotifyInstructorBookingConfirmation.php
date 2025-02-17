<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\NewLessonBooked;
use Modules\Drivisa\Entities\Lesson;
use Carbon\Carbon;

class NotifyInstructorBookingConfirmation implements ShouldQueue
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
    public function handle(NewLessonBooked $event)
    {
        $user = $event->instructor->user;
        $trainee = $event->trainee;
        $lesson = $event->lesson;

        $this->sendViaPostmark($user, $trainee, $lesson);
    }

    public function sendViaPostmark($user, $trainee, $lesson)
    {
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.instructor_lesson_booking'))
                    ->include([
                        'name' => $user->first_name . " " . $user->last_name,
                        "product_name" => "Drivisa",
                        "trainee_name" => $trainee->first_name . " " . $trainee->last_name,
                        'logo_url' => env('APP_LIVE_URL') . "/assets/media/logos/drivisa-logo200_80.svg",
                        'lesson_start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'lesson_start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'lesson_end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType()

                    ])
            );
    }
}
