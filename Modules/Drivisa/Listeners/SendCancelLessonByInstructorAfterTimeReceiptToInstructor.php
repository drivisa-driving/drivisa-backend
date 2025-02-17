<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Drivisa\Events\CancelLessonAfterTime;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class SendCancelLessonByInstructorAfterTimeReceiptToInstructor implements ShouldQueue
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
    public function handle(CancelLessonAfterTime $event)
    {
        $lesson = $event->lesson;
        $inCarInstructorFees = $event->inCarInstructorFees;
        $this->sendViaPostmark($lesson, $inCarInstructorFees);
    }

    public function sendViaPostmark($lesson, $inCarInstructorFees)
    {
        $this->mail->to($lesson->instructor->user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.cancel_lesson_receipt_to_instructor'))
                    ->include([
                        "product_name" => "Drivisa",
                        "date" => Carbon::now()->format('d-m-Y h:i A'),
                        'lesson_start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'lesson_start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'lesson_end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType(),
                        "name" => $lesson->instructor->user->first_name . " " . $lesson->instructor->user->last_name,
                        "instructor_fee" => "$" . CurrencyFormatter::getFormattedPrice($inCarInstructorFees),
                        "action_url" => "action_url_Value",
                        "billing_url" => "billing_url_Value",
                    ])
            );
    }
}
