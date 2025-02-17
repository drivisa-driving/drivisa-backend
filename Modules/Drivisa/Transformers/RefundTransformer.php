<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\LessonCancellation;
use Illuminate\Http\Resources\Json\ResourceCollection;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class RefundTransformer extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $refund_amount = $this->refund_amount ?: 0;
        $instructor_fee = $this->instructor_fee ?: 0;
        $drivisa_fee = $this->drivisa_fee ?: 0;

        $additional_costs = $this->lesson->additional_cost + $this->lesson->additional_tax;
        $instructor = Instructor::find($this->lesson->instructor_id);
        $lesson_type = str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE));
        $paymentBy = array_search($this->lesson->payment_by, Lesson::PAYMENT_BY);

        $refund = $this->getRefundAmount($this->cancel_by, $refund_amount, $this->lesson, $additional_costs);

        return [
            // Refund Details
            'id' => $this->id,
            'refund_to' => $this->lesson->trainee->first_name . " " . $this->lesson->trainee->last_name,
            'cancel_at' => Carbon::parse($this->cancel_at)->format('D, F d, M d, Y h:i A'),
            'cancel_by' => ucwords($this->cancel_by),
            'reason' => $this->reason,
            'instructor' => $instructor->first_name . " " . $instructor->last_name,
            'time_left' => $this->time_left ?: "0",
            'refund_id' => $this->refund_id,
            'instructor_fee' => "$" . CurrencyFormatter::getFormattedPrice($instructor_fee),
            'drivisa_fee' => "$" . CurrencyFormatter::getFormattedPrice($drivisa_fee),
            'refund' => $refund,
            'is_refunded' => $this->is_refunded !== NULL ? "Refunded" : "Not Refunded",
            'created_at' => $this->created_at,
            // Lesson Details
            'lesson' => $this->lesson,
            'lesson_type' => ucwords($lesson_type),
            'startAt_formatted' => Carbon::parse($this->lesson->start_at)->format('D, M d, Y h:i A'),
            'endAt_formatted' => Carbon::parse($this->lesson->end_at)->format('D, M d, Y h:i A'),
            'cost' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->cost),
            'commission' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->commission),
            'netAmount' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->net_amount),
            'additionalCost' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->additional_cost),
            'tax' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->tax),
            'additionalTax' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->additional_tax),
            'purchase_amount' => "$" . CurrencyFormatter::getFormattedPrice($this->lesson->purchase_amount),
            'paymentBy' => $paymentBy == 'stripe' ? 'Credit Card' : $paymentBy,
        ];
    }


    private function getRefundAmount($cancel_by, $refund_amount, $lesson, $additional_costs)
    {
        if ($cancel_by == 'instructor') {
            if ($lesson->lesson_type === Lesson::TYPE['bde']) {
                $refund = 'BDE Credit';
            } else if (
                $lesson->lesson_type !== Lesson::TYPE['bde']
                && $lesson->lesson_type !== Lesson::TYPE['g_test']
                && $lesson->lesson_type !== Lesson::TYPE['g2_test']
            ) {
                if ($lesson->cost > 0) {
                    $refund = "Cancelled Credit";
                } else {
                    $refund = "Credit";
                }

                if ($additional_costs > 0) {
                    $refund = "$" . CurrencyFormatter::getFormattedPrice($refund_amount);
                }

                return $refund;
            }
        } else {
            return "$" . CurrencyFormatter::getFormattedPrice($refund_amount);
        }
    }
}
