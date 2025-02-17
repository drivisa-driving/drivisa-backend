<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Notifications\ResetPickDropNotification;
use Modules\Drivisa\Notifications\ResetPickDropTraineeNotification;
use Stripe\Refund;
use Stripe\StripeClient;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Services\BookingService;
use Modules\Drivisa\Entities\CreditUseHistory;
use Modules\Drivisa\Entities\LessonPaymentLog;
use Modules\Drivisa\Exceptions\LessonStatusCanNotChangeException;
use Exception;
use Modules\Setting\Facades\Settings;

class ResetPickDropService
{
    public function __construct(private BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getExtraDistance($data)
    {
        $lesson = Lesson::where('id', $data['lesson_id'])->first();
        if (!$lesson) {
            throw new Exception('Lesson Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $workingHour = WorkingHour::where('id', $lesson->working_hour_id)->first();
        if (!$workingHour) {
            throw new Exception('Working Hour Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $point = $workingHour->point;
        if (!$point) {
            throw new Exception('Point Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $additional_charge = 0;
        $additional_tax = 0;
        $extra_distance = 0;
        $data['lesson_id'] = $workingHour->id;

        list(
            $data,
            $pick_point,
            $drop_point,
            $additional_charge,
            $additional_tax,
            $extra_distance
        ) = $this->bookingService->getAdditionalCostAndPoint($data, $point, $additional_charge, $additional_tax, $extra_distance);

        return [
            'lesson_id' => $lesson->id,
            'difference' =>  round(($lesson->additional_km - $extra_distance), 2),
            'new_additional_cost' => round(($additional_charge), 2),
            'new_additional_km' => round($extra_distance, 2),
            'new_additional_tax' => round($additional_tax, 2),
            'new_pickup_point' => $pick_point,
            'new_dropoff_point' => $drop_point,
            'amount' => round(($lesson->additional_km - $extra_distance) + ($lesson->additional_tax - $additional_tax), 2)
        ];
    }

    public function resetPickDrop($data)
    {
        $lesson = Lesson::where('id', $data['lesson_id'])->first();
        if (!$lesson) {
            throw new Exception('Lesson Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($lesson->reset_pick_drop === 1) {
            throw new Exception('You are allowed to reset pick-up and drop-off only once.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        throw_unless(
            $lesson->status === Lesson::STATUS['reserved'],
            new LessonStatusCanNotChangeException("Can't change status")
        );

        [$totalCost, $commission] = $this->getLessonCostCommission($lesson, $data);

        if ($data['difference'] < 0 && $data['payment_method_id']) {

            $paymentIntent = $this->bookingService->SavePaymentIntent($lesson->trainee, $data['payment_method_id'], round(abs($data['amount']), 2));

            if ($paymentIntent) {
                $this->createLessonPaymentLog($lesson, $paymentIntent);
                $this->bookingService->createTransactionHistory($paymentIntent);
            }
        } elseif ($data['difference'] > 0) {

            $additional_costs = $lesson->additional_cost + $lesson->additional_tax;

            if ($lesson->payment_intent_id && $additional_costs > 0)
                $this->refundAmount($lesson->payment_intent_id, $data['amount']);
        }

        $lesson->update([
            'pickup_point' => $data['new_pickup_point'],
            'dropoff_point' => $data['new_dropoff_point'],
            'additional_cost' => $data['new_additional_cost'],
            'additional_km' => $data['new_additional_km'],
            'additional_tax' => $data['new_additional_tax'],
            'net_amount' => $totalCost - $commission,
            'commission' => $commission,
            'reset_pick_drop' => 1
        ]);

        if(in_array($lesson->lesson_type, [Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
            $rentalRequest = RentalRequest::where('lesson_id', $lesson->id)->first();
            if($rentalRequest) {
                $rentalRequest->update([
                    'pickup_point' =>  stripslashes($data['new_pickup_point']),
                    'dropoff_point' => stripslashes($data['new_dropoff_point']),
                    'additional_tax' => $data['new_additional_tax'],
                    'additional_cost' => $data['new_additional_cost'],
                    'additional_km' => $data['new_additional_km'],
                    'total_distance' => floatval($lesson->additional_km + config('settings.free_distance', 10))
                ]);
            }
        }

        $lesson->instructor->user->notify(new ResetPickDropNotification($lesson, $lesson->trainee->user));
        $lesson->trainee->user->notify(new ResetPickDropTraineeNotification($lesson, $data['difference']));
    }

    private function getLessonCostCommission($lesson, $data)
    {
        $totalCost = $lesson->cost + $lesson->tax + $data['new_additional_cost'] + $data['new_additional_tax'];
        $totalCost = round($totalCost, 2);

        $commission = ($lesson->payment_by === Lesson::PAYMENT_BY['credit'])
            ? $lesson->commission
            : ($totalCost * Settings::get('commission') / 100);

        $commission = round($commission, 2);

        return [$totalCost, $commission];
    }

    private function getPaymentIntentFromCourse($lesson)
    {
        $courseUsedHistory = CreditUseHistory::where('id', $lesson->credit_use_histories_id)->first();
        return $courseUsedHistory->course->payment_intent_id;
    }

    private function createLessonPaymentLog($lesson, $paymentIntent)
    {
        LessonPaymentLog::create([
            'lesson_id' => $lesson->id,
            'charge_type' => LessonPaymentLog::CHARGE_TYPE['reset_pick_drop'],
            'transaction_id' => $paymentIntent?->charges->data[0]->balance_transaction,
            'payment_intent_id' => $paymentIntent?->id,
            'charge_id' => $paymentIntent?->charges->data[0]->id,
            'amount' => (float)($paymentIntent?->amount / 100)
        ]);
    }

    public function refundAmount($intent_id, $amount)
    {
        if (empty($intent_id)) {
            throw new \Exception('Payment Intent ID not found');
        }

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $intent = $stripe->paymentIntents->retrieve($intent_id);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $refundObject = [
            'payment_intent' => $intent->id,
            'amount' => (int)($amount * 100)
        ];
        Refund::create($refundObject);
    }
}
