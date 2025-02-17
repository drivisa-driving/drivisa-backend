<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Lesson;
use Modules\User\Entities\ReferralTransaction;
use Modules\Drivisa\Entities\InstructorEarning;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class EarningService
{
    public function getEarningBreakDownOld($instructor, $dateData)
    {
        $from = $dateData['from'] . " 00:00:00";
        $to = $dateData['to'] . " 23:59:59";
        $lessons = $instructor->lessons()
            ->whereBetween('ended_at', [$from, $to])
            ->whereNotNull('ended_at')
            ->orWhere(function ($query) use ($from, $to, $instructor) {
                $query
                    ->where('is_rescheduled', 1)
                    ->where('instructor_id', $instructor->id)
                    ->whereBetween('reschedule_time', [$from, $to]);
            })
            ->get();

        $lessonCancelled = $instructor->lessons()->whereHas(
            'lessonCancellation',
            function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])
                    ->whereNotNull('refund_id')
                    ->whereNotNull('time_left');
            }
        )->with('lessonCancellation')
            ->get();

        $data = [
            'lesson_earning' => CurrencyFormatter::getFormattedPrice(),  // output: 0.0
            'road_test_earning' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'additional_amount' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'compensation_earning' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'referral_amount' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'hst' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
        ];

        foreach ($lessons as $lesson) {
            if ($lesson->ended_at != null) {
                $data['lesson_earning'] += $lesson->getInstructorEarning();
                $data['road_test_earning'] += $lesson->getInstructorEarningForRoadTest();

                $data['additional_amount'] += $lesson->additional_cost;
            }

            if ($lesson->is_rescheduled == 1 && $lesson->rescheduled_payment_id != null) {
                if (in_array($lesson->lesson_type, [Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
                    if ($lesson->ended_at) {
                        $multiplier = $lesson->times_rescheduled == 0 ? 1 : $lesson->times_rescheduled;
                        $data['compensation_earning'] += $lesson->getInstructorRescheduledEarning() * $multiplier;
                    }
                } else {
                    $multiplier = $lesson->times_rescheduled == 0 ? 1 : $lesson->times_rescheduled;
                    $data['compensation_earning'] += $lesson->getInstructorRescheduledEarning() * $multiplier;
                }
            }
        }

        foreach ($lessonCancelled as $lessonCancel) {
            $data['compensation_earning'] += $lessonCancel->getInstructorCancelledEarning();
        }


        $data['referral_amount'] = ReferralTransaction::where('receiver_user_id', $instructor->user_id)
            ->whereBetween('created_at', [$from, $to])->sum('base_amount');

        $totalAmount = $data['lesson_earning'] + $data['road_test_earning'] + $data['additional_amount'] + $data['compensation_earning'] + $data['referral_amount'];

        $hst = $totalAmount * Lesson::STATIC_TAX_VALUE;

        return [
            'lesson_earning' => CurrencyFormatter::getFormattedPrice($data['lesson_earning']),
            'road_test_earning' => CurrencyFormatter::getFormattedPrice($data['road_test_earning']),
            'additional_amount' => CurrencyFormatter::getFormattedPrice($data['additional_amount']),
            'compensation_earning' => CurrencyFormatter::getFormattedPrice($data['compensation_earning']),
            'referral_amount' => CurrencyFormatter::getFormattedPrice($data['referral_amount']),
            'hst' => CurrencyFormatter::getFormattedPrice($hst)
        ];
    }

    public function getEarningBreakDown($instructor, $dateData)
    {
        $from = $dateData['from'] . " 00:00:00";
        $to = $dateData['to'] . " 23:59:59";

        $earnings = $instructor->instructorEarnings()
            ->with('lesson')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $data = [
            'lesson_earning' => CurrencyFormatter::getFormattedPrice(),  // output: 0.0
            'road_test_earning' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'additional_amount' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'compensation_earning' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'referral_amount' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
            'hst' => CurrencyFormatter::getFormattedPrice(), // output: 0.0
        ];

        foreach ($earnings as $earning) {

            if ($earning->type === InstructorEarning::TYPE['lesson_complete']) {

                $lessonType = $earning->lesson->lesson_type;

                if (in_array($lessonType, [Lesson::TYPE['car_rental'], Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
                    $data['road_test_earning'] +=  $earning->amount;
                } elseif (in_array($lessonType, [Lesson::TYPE['bde'], Lesson::TYPE['driving']])) {
                    $data['lesson_earning'] += $earning->amount;
                }

                $data['additional_amount'] += $earning->additional_cost;
            }

            if (in_array($earning->type, [InstructorEarning::TYPE['reschedule_lesson'], InstructorEarning::TYPE['cancel_lesson']])) {
                $data['compensation_earning'] += $earning->amount;
            }
        }

        $data['referral_amount'] = ReferralTransaction::where('receiver_user_id', $instructor->user_id)
            ->whereBetween('created_at', [$from, $to])->sum('base_amount');

        return [
            'lesson_earning' => CurrencyFormatter::getFormattedPrice($data['lesson_earning']),
            'road_test_earning' => CurrencyFormatter::getFormattedPrice($data['road_test_earning']),
            'additional_amount' => CurrencyFormatter::getFormattedPrice($data['additional_amount']),
            'compensation_earning' => CurrencyFormatter::getFormattedPrice($data['compensation_earning']),
            'referral_amount' => CurrencyFormatter::getFormattedPrice($data['referral_amount']),
            'hst' => CurrencyFormatter::getFormattedPrice($earnings->sum('tax'))
        ];
    }
}
