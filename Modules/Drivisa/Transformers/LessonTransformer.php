<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\DiscountUser;
use Modules\Drivisa\Entities\Lesson;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Services\BDEService;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Services\StatsService;
use Illuminate\Http\Resources\Json\JsonResource;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class LessonTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        $pickup_point = $this->pickup_point ? json_decode($this->pickup_point) : null;
        $dropoff_point = $this->dropoff_point ? json_decode($this->dropoff_point) : null;
        $instructor_evaluations = json_decode($this->instructor_evaluation);
        $lesson_type = str_replace('_', " ", array_search($this->lesson_type, Lesson::TYPE));
        $paymentBy = $this->payment_by == Lesson::PAYMENT_BY['stripe'] ? 'Credit Card' : 'credit';
        
        $discount = DiscountUser::with('user')->where('user_id',$this->trainee?->id)
            ->where('type_id',$this->id)->where('type',array_search($this->lesson_type, Lesson::TYPE))
            ->first();

        $discount = new DiscountUserTransformer($discount);
        return [
            'no' => $this->no,
            'lesson_type' => ucwords($lesson_type),
            'id' => $this->id,
            'startAt' => $this->start_at,
            'endAt' => $this->end_at,
            'duration' => $this->duration,
            'startAt_formatted' => Carbon::parse($this->start_at)->format('D, F d, Y h:i a'),
            'endAt_formatted' => Carbon::parse($this->end_at)->format('D, F d, Y h:i a'),
            'startAt_date_formatted' => Carbon::parse($this->start_at)->format('D, F d, Y'),
            'endAt_date_formatted' => Carbon::parse($this->end_at)->format('D, F d, Y'),
            'openAt_formatted' => Carbon::parse($this->start_at)->format('h:i a'),
            'closeAt_formatted' => Carbon::parse($this->end_at)->format('h:i a'),
            'total_bde_done' => BDEService::getTotalBdeDone($this->trainee_id),
            'startedAt' => $this->started_at,
            'endedAt' => $this->ended_at,
            'isRequest' => $this->is_request,
            'confirmed' => $this->confirmed,
            'cost' => CurrencyFormatter::getFormattedPrice($this->cost),
            'commission' => CurrencyFormatter::getFormattedPrice($this->commission),
            'netAmount' => CurrencyFormatter::getFormattedPrice($this->net_amount),
            'additionalCost' => CurrencyFormatter::getFormattedPrice($this->additional_cost),
            'additionalTax' => CurrencyFormatter::getFormattedPrice($this->additional_tax),
            'additionalKM' => $this->additional_km,
            'instructorLessonCost' => $this->getInstructorLessonCost(),
            'paymentBy' => $paymentBy,
            'tax' => CurrencyFormatter::getFormattedPrice($this->tax),
            'paidAt' => $this->paid_at,
            'status' => $this->status,
            'status_text' => array_search($this->status, Lesson::STATUS),
            'lesson_cancellation' => $this->lessonCancellation ?? null,
            'reason' => $this->lessonCancellation ? $this->lessonCancellation->reason : null,
            'cancel_by' => $this->lessonCancellation ? $this->lessonCancellation->cancel_by : null,
            'pickupPoint' => [
                'latitude' => $pickup_point?->latitude,
                'longitude' => $pickup_point?->longitude,
                'address' => $pickup_point?->address ?? null,
            ],
            'dropoffPoint' => [
                'latitude' => $dropoff_point?->latitude,
                'longitude' => $dropoff_point?->longitude,
                'address' => $dropoff_point?->address ?? null,
            ],
            'instructorNote' => $this->instructor_note,
            'instructorEvaluations' => $this->getInstructorEvaluations($instructor_evaluations),
            'traineeNote' => $this->trainee_note,
            'traineeEvaluation' => json_decode($this->trainee_evaluation) ?? null,
            'purchase_amount' => CurrencyFormatter::getFormattedPrice($this->purchase_amount),
            'lesson_bde_number_old' => $this->bde_number,
            'lesson_bde_number' => $this->getBdeNumber(),
            'rental_request_id' => $this->rental_request_id,
            'trainee_id' => $this->trainee_id,
            'trainee' => [
                'fullName' => $this->trainee->user->present()->fullname(),
                'avatar' => $this->trainee->user->present()->gravatar(),
                'username' => $this->trainee->user->username,
            ],
            'instructor_id' => $this->instructor_id,
            'is_refunded' => $this->lessonCancellation ? $this->lessonCancellation->is_refunded : null,
            'is_refund_initiated' => $this->is_refund_initiated,
            'working_hour_id' => $this->working_hour_id ?? null,
            'is_bonus_credit' => $this->is_bonus_credit,
            'instructor_details' => Instructor::find($this->instructor_id),
            'bdeLog' => $this->lesson_type == Lesson::TYPE['bde'] && $this->status == Lesson::STATUS['completed'] ?
                BDELog::where('lesson_id', $this->id)->first() : null,
            'instructor_location' => $this->getInstructorLocation($this->working_hour_id),
            'reschedule_lesson_fee' => $this->getRescheduledLessonFee(),
            // if lesson was rescheduled then get the last lesson details or new lesson details
            'rescheduleDetails' => $this->getRescheduleDetails(),
            'created_by' => $this->created_by,
            'transfer_id' => $this->transfer_id,
            'is_last_extra_hour_credit' => $this->isLastExtraHourCredit(),
            'can_reset_pick_drop' => $this->reset_pick_drop == 0,
            'discount_details'=>$discount
        ];
    }

    private function getInstructorEvaluations($instructor_evaluations): array
    {
        $result = [];
        if ($instructor_evaluations) {
            foreach ($instructor_evaluations as $evaluation) {
                $evaluationIndicator = [
                    'id' => $evaluation->id ?? null,
                    'title' => $evaluation->title ?? null,
                    'description' => $evaluation->description ?? null,
                    'points' => $evaluation->points ?? null,
                    'value' => $evaluation->value ?? null
                ];
                array_push($result, $evaluationIndicator);
            }
        }
        return $result;
    }

    /**
     * @return int
     */
    private function getInstructorLessonCost(): float
    {
        $tax = Settings::get('lesson_tax') / 100;
        $driving_fee = Settings::get('instructor_driving_fee');
        $bde_fee = Settings::get('instructor_bde_fee');
        $g_test_fee = Settings::get('instructor_g_test_fee');
        $g2_test_fee = Settings::get('instructor_g2_test_fee');

        if ($this->lesson_type === 1) {
            return ($driving_fee + ($driving_fee * $tax)) * $this->duration;
        }
        if ($this->lesson_type === 2) {
            return ($bde_fee + ($bde_fee * $tax)) * $this->duration;
        }

        if ($this->lesson_type === 4) {
            return $g_test_fee + ($g_test_fee * $tax);
        }

        if ($this->lesson_type === 5) {
            return $g2_test_fee + ($g2_test_fee * $tax);
        }
    }

    private function getInstructorLocation($working_hour_id)
    {
        $workingHour = WorkingHour::where('id', $working_hour_id)->first();
        return $workingHour?->point?->source_address ?? "";
    }

    private function getRescheduledLessonFee(): float
    {
        $fee = 0;
        if ($this->status === Lesson::STATUS['rescheduled'] && $this->is_rescheduled == 1 &&        $this->rescheduled_payment_id != null) {
            $tax = Settings::get('lesson_tax') / 100;
            $in_car_bde_fees = 20 + (20 * $tax);
            $road_test_fees = 50 + (50 * $tax);

            if (in_array($this->lesson_type, [Lesson::TYPE['driving'], Lesson::TYPE['bde']])) {
                $fee = $in_car_bde_fees * $this->duration;
            }
            if (in_array($this->lesson_type, [Lesson::TYPE['g_test'], Lesson::TYPE['g2_test']])) {
                $fee = $road_test_fees;
            }
        }

        return CurrencyFormatter::getFormattedPrice($fee);
    }

    private function getRescheduleDetails()
    {
        $lastLesson = Lesson::find($this->last_lesson_id);
        $newLesson = Lesson::where('last_lesson_id', $this->id)->first();

        $data = [];

        if ($lastLesson) {
            $data['last_lesson_id'] = $lastLesson->id;
            $data['last_lesson_no'] = $lastLesson->no;
            $data['last_startAt_formatted'] = Carbon::parse($lastLesson->start_at)->format('D, F d, Y h:i a');
            $data['last_endAt_formatted'] = Carbon::parse($lastLesson->end_at)->format('D, F d, Y h:i a');
            $data['last_status_text'] = array_search($lastLesson->status, Lesson::STATUS);
        }

        if ($newLesson) {
            $data['new_lesson_id'] = $newLesson->id;
            $data['new_lesson_no'] = $newLesson->no;
            $data['new_startAt_formatted'] = Carbon::parse($newLesson->start_at)->format('D, F d, Y h:i a');
            $data['new_endAt_formatted'] = Carbon::parse($newLesson->end_at)->format('D, F d, Y h:i a');
            $data['new_status_text'] = array_search($newLesson->status, Lesson::STATUS);
        }

        return $data;
    }

    private function isLastExtraHourCredit()
    {
        $bdeNumber = $this->getBdeNumber();
        if ($bdeNumber <= 10) return false;
        $statsService = app(StatsService::class);
        $remainingHours = str_replace('.', '', $statsService->getStatsByType($this->trainee->user, "BDE", true)['data']['remaining_hours']);
        $todayUpcomingLessonsCount = $this->trainee->lessons
            ->where('lesson_type', Lesson::TYPE['bde'])
            ->where('status', Lesson::STATUS['reserved'])
            ->where('end_at', '>', now())
            ->count();
        return $remainingHours === '0' && $todayUpcomingLessonsCount === 0;
    }

    private function getBdeNumber()
    {
        return $this->trainee
            ->lessons
            ->filter(
                fn ($lesson) => !is_null($lesson->ended_at) ||
                    ($lesson->end_at < now() && in_array($lesson->status, [
                        Lesson::STATUS['reserved'],
                        Lesson::STATUS['inProgress']
                    ]))
            )
            ->where('lesson_type', Lesson::TYPE['bde'])
            ->sum('duration');
    }
}
