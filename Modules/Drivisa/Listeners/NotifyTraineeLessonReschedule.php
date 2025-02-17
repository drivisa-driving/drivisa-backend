<?php

namespace Modules\Drivisa\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SendGridMailable;
use Modules\Drivisa\Events\LessonReschedule;
use Modules\User\Contracts\Authentication;
use Modules\Drivisa\Entities\Lesson;
use Carbon\Carbon;

class NotifyTraineeLessonReschedule
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
    public function handle(LessonReschedule $event)
    {
        $trainee = $event->trainee->user;
        $lesson = $event->lesson;
        $this->sendViaPostmark($trainee, $lesson);
    }

    public function sendViaPostmark($trainee, $lesson)
    {
        $this->mail->to($trainee->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.lesson_rescheduled_trainee_notify'))
                    ->include([
                        'name' => $trainee->first_name . " " . $trainee->last_name,
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
