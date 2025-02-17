<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Drivisa\Events\LessonComplete;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReceiptToInstructorAfterLessonComplete implements ShouldQueue
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

    public Mailer $mail;

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
    public function handle(LessonComplete $event)
    {
        $this->sendViaPostmark(
            $event->instructor->user,
            $event->lesson,
            $event->amount,
        );
    }

    public function sendViaPostmark($user, $lesson, $amount)
    {
        $hst = ($amount * 13) / 100;
        $this->mail->to($user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.instructor_receipt_after_lesson_complete'))
                    ->include([
                        "product_name" => "Drivisa",
                        "name" => $user->first_name . " " . $user->last_name,
                        'lesson_start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'lesson_start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'lesson_end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType(),
                        "date" => Carbon::now()->format('d-m-Y h:i A'),
                        "hst" => "$" . $hst,
                        "total" => "$" . $amount,
                        "action_url" => "action_url_Value",
                    ])
            );
    }
}
