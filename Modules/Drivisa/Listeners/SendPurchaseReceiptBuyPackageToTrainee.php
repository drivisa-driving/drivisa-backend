<?php

namespace Modules\Drivisa\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Drivisa\Entities\Discount;
use Modules\Drivisa\Events\NewBuyPackage;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Modules\Drivisa\Entities\DiscountUser;

class SendPurchaseReceiptBuyPackageToTrainee implements ShouldQueue
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
    public function handle(NewBuyPackage $event)
    {
        $trainee = $event->trainee;
        $package = $event->package;
        $totalChargeable = $event->totalChargeable;
        $discountPrice = $event->discountPrice;

        $this->sendViaPostmark($trainee, $package, $totalChargeable, $discountPrice);
    }

    public function sendViaPostmark(
        $trainee,
        $package,
        $totalChargeable,
        $discountPrice
    )
    {
        $tax = $totalChargeable - $discountPrice;
        $discount = DiscountUser::where('type', 'buy_package')
            ->where('user_id', $trainee->id)->where('type_id', $package->id)->first();
        $this->mail->to($trainee->user->email)
            ->send((new \Coconuts\Mail\PostmarkTemplateMailable())
                ->identifier(config('template.postmark.student_package_purchase_receipt'))
                ->include([
                    "product_name" => "Drivisa",
                    "name" => $trainee->user->first_name . " " . $trainee->user->last_name,
                    "date" => Carbon::now()->format('d-m-Y h:i A'),
                    "description" => "Thanks for payment",
                    'package_name' => $package->name,
                    'credit_received' => $package->packageData->hours . " Hours",
                    'sale_price' => "$" . CurrencyFormatter::getFormattedPrice($package->packageData->discount_price),
                    'discount_price' => "$" . CurrencyFormatter::getFormattedPrice($discountPrice),
                    'discount' =>  '$' . $discount?->total_discount??0 ,
                    'tax' => "$" . CurrencyFormatter::getFormattedPrice($tax),
                    'total_charge' => "$" . CurrencyFormatter::getFormattedPrice($totalChargeable),
                    "action_url" => "action_url_Value",
                    "billing_url" => "billing_url_Value",
                ])
            );
    }
}
