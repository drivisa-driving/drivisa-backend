<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Drivisa\Events\NewLessonBooked;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Modules\Drivisa\Entities\DiscountUser;

class SendPurchaseReceiptToTrainee implements ShouldQueue
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
    public function handle(NewLessonBooked $event)
    {
        $this->sendViaPostmark(
            $event->trainee,
            $event->purchase,
            $event->transaction,
            $event->lesson,
            $event->is_credit,
            $event->cost,
            $event->tax,
            $event->additional_charge,
            $event->additional_tax,
            $event->extra_distance,
            $event->commission,
            $event->totalCost,
            $event->totalDiscount,
        );
    }

    public function sendViaPostmark(
        $trainee,
        $purchase,
        $transaction,
        $lesson,
        $is_credit,
        $cost,
        $tax,
        $additional_charge,
        $additional_tax,
        $extra_distance,
        $commission,
        $totalCost,
        $totalDiscount
    )
    {
        if (!$is_credit) {
            if ($additional_charge > 0) {
                $totalCost = $totalCost - ($cost + $tax);
                $start_at = Carbon::parse($lesson->start_at);
                $end_at = Carbon::parse($lesson->end_at);
                $cost = $start_at->diffInHours($end_at) . " Credit";
                $tax = 0;
            } else {
                $cost = "$" . CurrencyFormatter::getFormattedPrice($cost);
            }

            $this->mail->to($trainee->user->email)
                ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                    ->identifier(config('template.postmark.student_purchase_receipt'))
                    ->include([
                        "product_name" => "Drivisa",
                        "name" => $trainee->user->first_name . " " . $trainee->user->last_name,
                        "receipt_id" => $purchase->id,
                        'lesson_start_at' => Carbon::parse($lesson->start_at)->format('D, F d, Y'),
                        'lesson_start_time' => Carbon::parse($lesson->start_at)->format('h:i A'),
                        'lesson_end_time' => Carbon::parse($lesson->end_at)->format('h:i A'),
                        'lesson_type' => $lesson->lessonType(),
                        "date" => Carbon::now()->format('d-m-Y h:i A'),
                        "description" => "Thanks for payment",
                        "cost" => $cost,
                        'discount' =>  '$' . $totalDiscount ??0,
                        "tax" => "$" . CurrencyFormatter::getFormattedPrice($tax),
                        "additional_tax" => "$" . CurrencyFormatter::getFormattedPrice($additional_tax, 2),
                        "extra_distance" => "$" . CurrencyFormatter::getFormattedPrice($extra_distance, 2),
                        "total_cost" => "$" . CurrencyFormatter::getFormattedPrice($totalCost),
                        "action_url" => "action_url_Value",
                        "billing_url" => "billing_url_Value",
                    ])
                );
        }
    }
}
