<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Drivisa\Entities\Lesson;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\InstructorEarning;

class SyncInstructorEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'earnings:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync instructor earnings for previous lessons';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tax = Settings::get('lesson_tax') / 100;

        $completedLessons = Lesson::whereNotNull('ended_at')->get();

        $rescheduledLessons = Lesson::where('is_rescheduled', 1)
            ->whereNotNull('reschedule_time')
            ->whereNotNull('rescheduled_payment_id')
            ->get();

        $lessonCancelled = Lesson::whereHas(
            'lessonCancellation',
            function ($query) {
                $query->whereNotNull('refund_id')
                    ->whereNotNull('time_left');
            }
        )->with('lessonCancellation')
            ->get();

        foreach ($completedLessons as $completedLesson) {
            if ($completedLesson->ended_at != null) {
                if (in_array($completedLesson->lesson_type, [Lesson::TYPE['car_rental'], Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
                    $amount =  $completedLesson->getInstructorEarningForRoadTest();
                } elseif (in_array($completedLesson->lesson_type, [Lesson::TYPE['bde'], Lesson::TYPE['driving']])) {
                    $amount = $completedLesson->getInstructorEarning();
                }

                $additionalCost = $completedLesson->additional_cost;
                $hst = ($amount + $additionalCost) * $tax;
                $totalAmount = $amount + $additionalCost + $hst;

                $this->instructorEarning($completedLesson, "lesson_complete", $amount, $hst, $totalAmount, $additionalCost);
            }
        }

        foreach ($rescheduledLessons as $rescheduledLesson) {

            if (in_array($rescheduledLesson->lesson_type, [Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
                if ($rescheduledLesson->ended_at) {
                    $multiplier = $rescheduledLesson->times_rescheduled == 0 ? 1 : $rescheduledLesson->times_rescheduled;
                    $amount = $rescheduledLesson->getInstructorRescheduledEarning() * $multiplier;
                }
            } else {
                $multiplier = $rescheduledLesson->times_rescheduled == 0 ? 1 : $rescheduledLesson->times_rescheduled;
                $amount = $rescheduledLesson->getInstructorRescheduledEarning() * $multiplier;
            }

            $hst = $amount * $tax;
            $totalAmount = $amount + $hst;

            $this->instructorEarning($rescheduledLesson, "reschedule_lesson", $amount, $hst, $totalAmount);
        }

        foreach ($lessonCancelled as $lessonCancel) {
            $amount = $lessonCancel->getInstructorCancelledEarning();
            $hst = $amount * $tax;
            $totalAmount = $amount + $hst;

            $this->instructorEarning($lessonCancel, "cancel_lesson", $amount, $hst, $totalAmount);
        }
    }

    private function instructorEarning($lesson, $type, $amount = 0, $hst = 0, $totalAmount = 0, $additionalCost = 0)
    {
        $instructorEarning = InstructorEarning::create([
            'lesson_id' => $lesson->id,
            'instructor_id' => $lesson->instructor_id,
            'type' => InstructorEarning::TYPE[$type],
            'amount' => $amount,
            'additional_cost' => $additionalCost,
            'tax' => $hst,
            'total_amount' => $totalAmount,
        ]);

        if ($instructorEarning->type === InstructorEarning::TYPE['lesson_complete']) {
            $instructorEarning->update([
                'created_at' => $lesson->ended_at,
                'updated_at' => $lesson->ended_at,
            ]);
        } else if ($instructorEarning->type === InstructorEarning::TYPE['reschedule_lesson']) {
            $instructorEarning->update([
                'created_at' => $lesson->reschedule_time,
                'updated_at' => $lesson->reschedule_time,
            ]);
        } else if ($instructorEarning->type === InstructorEarning::TYPE['cancel_lesson']) {
            $instructorEarning->update([
                'created_at' => $lesson->lessonCancellation->created_at,
                'updated_at' => $lesson->lessonCancellation->created_at,
            ]);
        }
    }
}
