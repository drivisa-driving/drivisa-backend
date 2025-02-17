<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Events\NewCarRentalBooked;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Modules\Drivisa\Entities\DiscountUser;

class SendPurchaseReceiptBuyCarRentalToTrainee implements ShouldQueue
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
    public function handle(NewCarRentalBooked $event)
    {
        $rentalRequest = $event->rentalRequest;
        $packageData = $event->packageData;
        $tax = $event->tax;
        $totalCost = $event->totalCost;
        $discountAmount = $event->discountAmount;
        $this->sendViaPostmark($rentalRequest, $packageData, $tax, $totalCost,$discountAmount);
    }

    public function sendViaPostmark(
        $rentalRequest,
        $packageData,
        $tax,
        $totalCost,
        $discountAmount
    )
    {

        $total = ($packageData->discount_price - $discountAmount ?? 0) + $tax + $rentalRequest->additional_tax + $rentalRequest->additional_km;

        $this->mail->to($rentalRequest->trainee->user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.student_car_rental_purchase_receipt'))
                ->include([
                    "product_name" => "Drivisa",
                    "name" => $rentalRequest->trainee->first_name . " " . $rentalRequest->trainee->last_name,
                    "date" => Carbon::parse($rentalRequest->booking_date)->format('d-m-Y h:i A'),
                    "time" => Carbon::parse($rentalRequest->booking_time)->format('h:i A'),
                    "created_date" => Carbon::now()->format('d-m-Y h:i A'),
                    "description" => "Thanks for payment",
                    'purchase_id' => $rentalRequest->purchase_id,
                    'amount' => "$" . CurrencyFormatter::getFormattedPrice($packageData->discount_price),
                    'discount' => '$' . $discountAmount,
                    'lesson_tax' => "$" . CurrencyFormatter::getFormattedPrice($tax),
                    'tax' => "$" . CurrencyFormatter::getFormattedPrice($rentalRequest->additional_tax),
                    'additional_km' => "$" . CurrencyFormatter::getFormattedPrice($rentalRequest->additional_km),
                    'total_cost' => "$" . CurrencyFormatter::getFormattedPrice($total),
                    "action_url" => "action_url_Value",
                    "billing_url" => "billing_url_Value",
                ])
            );
    }
}
