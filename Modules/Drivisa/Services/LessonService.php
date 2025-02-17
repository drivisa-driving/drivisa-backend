<?php

namespace Modules\Drivisa\Services;

use stdClass;
use Exception;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Faker\Provider\Payment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Events\CancelLesson;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Events\LessonComplete;
use Modules\Drivisa\Services\CourseService;
use Modules\Drivisa\Events\LessonReschedule;
use Modules\Drivisa\Entities\CreditUseHistory;
use Modules\Drivisa\Entities\InstructorEarning;
use Modules\Drivisa\Entities\LessonPaymentLog;
use Modules\Drivisa\Events\CancelLessonAfterTime;
use Modules\Drivisa\Events\CancelLessonByTrainee;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Transformers\LessonTransformer;
use Modules\Drivisa\Events\CancelLessonByInstructor;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;
use Modules\Drivisa\Transformers\LessonTraineeTransformer;
use Modules\Drivisa\Transformers\LessonInstructorTransformer;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;
use Modules\Drivisa\Exceptions\LessonStatusCanNotChangeException;
use Modules\Drivisa\Notifications\TraineeCancelLessonNotification;
use Modules\Drivisa\Notifications\InstructorCancelLessonNotification;
use Modules\Drivisa\Notifications\TraineeLessonRescheduleNotification;
use Modules\Drivisa\Exceptions\LessonStatusChangeRoleNotFoundException;
use Modules\Drivisa\Notifications\InstructorCancelLessonTraineeNotification;
use Modules\Drivisa\Notifications\TraineeCancelLessonInstructorNotification;
use Modules\User\Notifications\InstructorPromotionMoneyTransferNotification;
use Modules\Drivisa\Notifications\InstructorTraineeLessonRescheduledNotification;
use Modules\Drivisa\Notifications\InstructorBookingReceviedNotification;
use Modules\Setting\Contracts\Setting;

class LessonService
{
    const INSTRUCTOR_PROMOTIONS_AMOUNT_FOR_LESSON_COMPLETED = 500; # $500
    const IN_CAR_INSTRUCTOR_CANCEL_FEE = 20;
    const IN_CAR_DRIVISA_CANCEL_FEE = 5;
    const ROAD_TEST_CANCELLATION_FEE = 60;
    const INSTRUCTOR_ROAD_TEST_RESCHEDULE_FEE = 50;
    const STATIC_TAX_VALUE = 0.13; # conversion of 13/100


    private $lessonRepository;
    private $evaluationIndicatorRepository;
    private LessonCancellationService $lessonCancellationService;
    private $courseService;
    private StripeService $stripeService;
    private WorkingHourRepository $workingHourRepository;
    private WorkingDayRepository $workingDayRepository;

    private InstructorRepository $instructorRepository;
    private TransactionService $transactionService;
    private CourseRepository $courseRepository;
    private PaymentTransferService $paymentTransferService;

    /**
     * @param LessonRepository $lessonRepository
     * @param EvaluationIndicatorRepository $evaluationIndicatorRepository
     * @param LessonCancellationService $lessonCancellationService
     * @param CourseService $courseService
     * @param StripeService $stripeService
     * @param WorkingHourRepository $workingHourRepository
     * @param WorkingDayRepository $workingDayRepository
     * @param InstructorRepository $instructorRepository
     * @param TransactionService $transactionService
     * @param CourseRepository $courseRepository
     * @param PaymentTransferService $paymentTransferService
     */
    public function __construct(
        LessonRepository              $lessonRepository,
        EvaluationIndicatorRepository $evaluationIndicatorRepository,
        LessonCancellationService     $lessonCancellationService,
        CourseService                 $courseService,
        StripeService                 $stripeService,
        WorkingHourRepository         $workingHourRepository,
        WorkingDayRepository          $workingDayRepository,
        InstructorRepository          $instructorRepository,
        TransactionService            $transactionService,
        CourseRepository              $courseRepository,
        PaymentTransferService        $paymentTransferService
    ) {
        $this->lessonRepository = $lessonRepository;
        $this->evaluationIndicatorRepository = $evaluationIndicatorRepository;
        $this->lessonCancellationService = $lessonCancellationService;
        $this->courseService = $courseService;
        $this->stripeService = $stripeService;
        $this->workingHourRepository = $workingHourRepository;
        $this->workingDayRepository = $workingDayRepository;
        $this->instructorRepository = $instructorRepository;
        $this->transactionService = $transactionService;
        $this->courseRepository = $courseRepository;
        $this->paymentTransferService = $paymentTransferService;
    }

    public function searchLessons($data)
    {
        return $this->lessonRepository->searchLessons($data);
    }

    public function getInstructorLesson($instructor, $lesson)
    {
        if ($instructor->id != $lesson->instructor_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            return $lesson;
        }
    }

    public function getInstructorHistory($instructor, $data)
    {
        $per_page = $data['per_page'] ?? 50;

        return $this->lessonRepository->where('instructor_id', $instructor->id)
            ->whereDate('start_at', "<", today())
            ->orderByRaw('DATE(start_at) DESC')->orderBy('start_time')
            ->paginate($per_page);
    }

    public function getTraineeLesson($trainee, $lesson)
    {
        if ($trainee->id != $lesson->trainee_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            return $lesson;
        }
    }

    public function updateStartedAt($instructor, $lesson)
    {
        if ($instructor->id != $lesson->instructor_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            $startAt = Carbon::parse($lesson->start_at);
            $endAt = Carbon::parse($lesson->end_at);
            $now = Carbon::now();
            if ($startAt->greaterThan($now->copy()->addMinutes(15))) {
                throw new Exception(trans(
                    'drivisa::drivisa.messages.start_at_greater_than_now',
                    ['start_at' => $startAt->format('Y-m-d h:i a')]
                ), Response::HTTP_CONFLICT);
            } else if ($now->greaterThan($endAt->copy()->addMinutes(15))) {
                throw new Exception(trans(
                    'drivisa::drivisa.messages.end_at_less_than_now',
                    ['end_at' => $endAt->format('Y-m-d h:i a')]
                ), Response::HTTP_CONFLICT);
            } else {
                $lesson->update(['started_at' => $now, 'status' => Lesson::STATUS['inProgress']]);
            }
        }
    }

    public function updateEndedAt($instructor, $lesson)
    {
        if ($instructor->id != $lesson->instructor_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            $lesson->update([
                'started_at' => $lesson->start_at,
                'ended_at' => $lesson->end_at,
                'status' => Lesson::STATUS['completed']
            ]);

            $transfer = $this->createTransfer($lesson, $instructor);
            $lesson->transfer_id = $transfer->id;
            $lesson->save();

            // stop sending promotion money to the instructor when they complete 100 hours as per suggestions...

            // if ($instructor->completedLessonHours(100) && $instructor->promotion_level == 1) {
            //     if ($this->sendPromotionMoney($instructor)) {
            //         $instructor->update(['promotion_level' => 2]);
            //         $instructor
            //             ->user
            //             ->notify(new InstructorPromotionMoneyTransferNotification(self::INSTRUCTOR_PROMOTIONS_AMOUNT_FOR_LESSON_COMPLETED));
            //     }
            // }
        }
    }

    public function sendPromotionMoney($instructor): bool
    {
        $instructor_account = $instructor->stripeAccount;

        $isMoneySent = false;
        if ($instructor_account) {
            $this->paymentTransferService->createTransfer(self::INSTRUCTOR_PROMOTIONS_AMOUNT_FOR_LESSON_COMPLETED, $instructor_account);
            $isMoneySent = true;
        }

        return $isMoneySent;
    }

    public function updateInstructorEvaluation($instructor, $lesson, $data)
    {
        if ($instructor->id != $lesson->instructor_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            if ($lesson->ended_at) {
                if ($data['instructor_evaluation'] ?? null) {

                    $evaluationCollection = collect($data['instructor_evaluation']);

                    $evaluationWhichNotInRequests = $this->evaluationIndicatorRepository
                        ->query()
                        ->whereNotIn('id', $evaluationCollection->pluck('id')->toArray())
                        ->get();

                    $result = [];


                    foreach ($data['instructor_evaluation'] as $evaluation) {
                        $evaluation_indicator = $this->evaluationIndicatorRepository->find($evaluation['id']);
                        $result[] = [
                            'id' => $evaluation_indicator->id,
                            'title' => $evaluation_indicator->title,
                            'description' => $evaluation_indicator->description,
                            'points' => $evaluation_indicator->points,
                            'value' => $evaluation['value'],
                        ];
                    }

                    foreach ($evaluationWhichNotInRequests as $single_evaluation) {
                        $result[] = [
                            'id' => $single_evaluation->id,
                            'title' => $single_evaluation->title,
                            'description' => $single_evaluation->description,
                            'value' => "NA",
                            'points' => $single_evaluation->points,
                        ];
                    }


                    $data['instructor_evaluation'] = json_encode($result);
                } else {
                    $data['instructor_evaluation'] = null;
                }
                $lesson->update($data);
            } else {
                throw new Exception(trans('drivisa::drivisa.messages.evaluation_after_ended_lesson'), Response::HTTP_CONFLICT);
            }
        }
    }

    public function updateTraineeEvaluation($trainee, $lesson, $data)
    {
        if ($trainee->id != $lesson->trainee_id) {
            throw new Exception(trans('drivisa::drivisa.messages.access_denied'), Response::HTTP_FORBIDDEN);
        } else {
            if ($lesson->ended_at) {
                if ($data['trainee_evaluation'] ?? null) {
                    $data['trainee_evaluation'] = json_encode(
                        [
                            'value' => $data['trainee_evaluation']
                        ]
                    );
                } else {
                    $data['trainee_evaluation'] = null;
                }
                $lesson->update($data);
            } else {
                throw new Exception(trans('drivisa::drivisa.messages.evaluation_after_ended_lesson'), Response::HTTP_CONFLICT);
            }
        }
    }

    /**
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForCancelLesson($lesson): void
    {
        $eventVariables = [
            $lesson
        ];

        event(new CancelLesson(...$eventVariables));
    }

    /**
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForCancelLessonByTrainee($lesson, $cancellationFee, $refundAmount, $instructor_refund_amount): void
    {
        $eventVariables = [
            $lesson,
            $cancellationFee,
            $refundAmount,
            $instructor_refund_amount
        ];

        event(new CancelLessonByTrainee(...$eventVariables));
    }

    public function cancelLesson($cancel_by, $lesson, $data)
    {
        throw_unless(
            in_array($cancel_by, ['trainee', 'admin', 'instructor']),
            new LessonStatusChangeRoleNotFoundException("Not Cancel Role Found")
        );

        $cancelled_lesson = $this->lessonCancellationService->cancel($lesson, [
            'cancel_by' => $cancel_by,
            'reason' => $data['reason']
        ]);

        if ($cancel_by === 'trainee') {

            $this->validateTraineeCancellation($lesson);

            $cancelled_lesson->time_left = $this->calculateTimeLeftToStartLesson($lesson);

            $cancellationFee = $lesson->lesson_type !== Lesson::TYPE['driving']
                ? $this->getCancellationFeeFromAmount(self::ROAD_TEST_CANCELLATION_FEE)
                : $this->getCancellationFee($lesson);

            $instructor_refund_amount = $this->getInstructorRefundAmount($lesson);

            $lessonPaymentLogs = LessonPaymentLog::where('lesson_id', $lesson->id)->get();

            if ($lesson->payment_intent_id && $lesson->cost != 0.00) {

                $refund = $this->stripeService->refund(
                    $lesson->payment_intent_id,
                    $cancellationFee,
                    $lesson->instructor->stripeAccount->account_id,
                    $instructor_refund_amount
                );

                $refundAmount = 0;
                $refundAmount += ($refund ? $refund->amount / 100 : 0);

                $refundAmount += $this->refundLessonPaymentLogs($lessonPaymentLogs);

                $this->instructorEarning($lesson, $instructor_refund_amount, "cancel_lesson");

                $this->updateCancelledLessonDetails($cancelled_lesson, $refund, $cancellationFee, $instructor_refund_amount, $refundAmount);

                $this->allEventVariablesThatNeedForCancelLessonByTrainee($lesson, $cancellationFee, $refundAmount, $instructor_refund_amount);
            } else if ($lesson->payment_by === Lesson::PAYMENT_BY['credit']) {

                // calculate price from package and refund money
                $paymentIntentID = $this->getPaymentIntentFromCourse($lesson);

                $refundAmount = 0;

                if ($paymentIntentID) {
                    $totalAmount = $this->getCancellationAmountForCreditLesson($lesson, $cancellationFee);
                    $instructor_refund_amount = $this->getInstructorRefundAmount($lesson);

                    $refund = $this->stripeService->refundUpdated(
                        $paymentIntentID,
                        $totalAmount,
                        $lesson->instructor->stripeAccount->account_id,
                        $instructor_refund_amount
                    );

                    $refundAmount += ($refund ? $refund->amount / 100 : 0);

                    $this->instructorEarning($lesson, $instructor_refund_amount, "cancel_lesson");
                }

                $additional_costs = $lesson->additional_cost + $lesson->additional_tax;

                if ($lesson->payment_intent_id && $additional_costs > 0) {
                    $refundAdditionalCharge = $this->stripeService->refund($lesson->payment_intent_id);

                    $refundAmount += ($refundAdditionalCharge ? $refundAdditionalCharge->amount / 100 : 0);
                }

                $refundAmount += $this->refundLessonPaymentLogs($lessonPaymentLogs);

                $this->updateCancelledLessonDetails($cancelled_lesson, $refund, $cancellationFee, $instructor_refund_amount, $refundAmount);

                $this->allEventVariablesThatNeedForCancelLessonByTrainee($lesson, $cancellationFee, $refundAmount, $instructor_refund_amount);
            }

            $this->whenCancelLessonWorkingHourRelease($lesson);

            $this->notifyInstructorsAndTrainee($lesson);
        } else if ($cancel_by === 'instructor') {
            $this->cancelLessonByInstructor($lesson, $cancelled_lesson);
        }

        $lesson->update(['status' => Lesson::STATUS['canceled']]);
    }

    private function validateTraineeCancellation($lesson)
    {
        throw_if(
            $lesson->bde_number != null,
            new LessonStatusChangeRoleNotFoundException("Can't Cancel BDE Lesson")
        );

        throw_if(
            $lesson->is_bonus_credit !== 0,
            new LessonStatusChangeRoleNotFoundException("Can't Cancel Bonus Credit Lesson")
        );

        throw_if(
            $lesson->status === Lesson::STATUS['inProgress'],
            new LessonStatusChangeRoleNotFoundException("Can't Cancel In Progress Lesson")
        );
    }

    private function calculateTimeLeftToStartLesson($lesson)
    {
        return Carbon::now()->diffInHours(Carbon::parse($lesson->start_at));
    }

    private function updateCancelledLessonDetails($cancelled_lesson, $refund, $cancellationFee, $instructor_refund_amount, $refundAmount)
    {
        $cancelled_lesson->update([
            'refund_id' => $refund?->id,
            'instructor_fee' => $instructor_refund_amount,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount,
        ]);
    }

    private function notifyInstructorsAndTrainee($lesson)
    {
        $lesson->instructor->user->notify(new TraineeCancelLessonInstructorNotification($lesson));
        $lesson->trainee->user->notify(new TraineeCancelLessonNotification($lesson));
    }

    /**
     * @param $dateTime
     * @param $timeInMinutes
     * @return bool
     */
    private function isLessonTimeExceed($dateTime, $timeInMinutes = 15): bool
    {
        return Carbon::parse($dateTime)->addMinutes($timeInMinutes)->lte(now());
    }

    /**
     * @param $lesson
     * @return float
     */
    private function getCancellationFee($lesson): float
    {
        $baseAmount = self::IN_CAR_DRIVISA_CANCEL_FEE + self::IN_CAR_INSTRUCTOR_CANCEL_FEE;
        $amount = $baseAmount * $lesson->duration;

        $tax = Settings::get('lesson_tax');
        return $amount + ($amount * ($tax / 100));
    }

    /**
     * @param $lesson
     * @return float
     */
    private function getCancellationFeeFromAmount($amount): float
    {
        $tax = Settings::get('lesson_tax');
        return $amount + ($amount * ($tax / 100));
    }

    private function getCancellationAmountForCreditLesson($lesson, $cancellationFee = null)
    {
        $courseUsedHistory = CreditUseHistory::where('id', $lesson->credit_use_histories_id)->first();

        $course = $courseUsedHistory->course;
        $package = $course->package;

        $transaction = $course->purchase->transaction;

        $packageData = $package->packageData;


        $amount = $this->calculateHourlyPrice($transaction->amount, $packageData->hours);


        $amount *= $courseUsedHistory->credit_used;

        $refundAmount = $amount - $cancellationFee ?? 0;

        return $transaction->amount - $refundAmount;
    }

    /**
     * @param $totalAmount
     * @param $totalCredit
     * @return float|int
     */
    public function calculateHourlyPrice($totalAmount, $totalCredit): float|int
    {
        return $totalAmount / $totalCredit;
    }

    public function getPaymentIntentFromCourse($lesson)
    {
        $courseUsedHistory = CreditUseHistory::where('id', $lesson->credit_use_histories_id)->first();
        return $courseUsedHistory->course->payment_intent_id;
    }

    /**
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForCancelLessonAfterTime($lesson, $inCarInstructorFees, $cancellationFee, $refund): void
    {
        $eventVariables = [
            $lesson,
            $inCarInstructorFees,
            $cancellationFee,
            $refund
        ];

        event(new CancelLessonAfterTime(...$eventVariables));
    }

    public function cancelLessonWhenTraineeNotAvailable($lesson)
    {
        throw_unless(
            $lesson->status === Lesson::STATUS['reserved'],
            new LessonStatusCanNotChangeException("Can't change status")
        );

        $cancelled_lesson = $this->lessonCancellationService->cancel($lesson, [
            'cancel_by' => 'instructor',
            'reason' => 'Trainee Not reached at training point'
        ]);

        $refund = null;
        $additionalCharges = null;
        $refundAmount = 0;
        $lessonPaymentLogs = LessonPaymentLog::where('lesson_id', $lesson->id)->get();

        list($instructorFee, $drivisaFee, $cancellationFee) = $this->cancellationFeeAfertStartTime($lesson);

        if ($lesson->payment_intent_id && $lesson->cost != 0.00) {
            $refund = $this->stripeService->refund(
                $lesson->payment_intent_id,
                $cancellationFee,
                $lesson->instructor->stripeAccount->account_id,
                $instructorFee
            );

            $refundAmount += ($refund ? $refund->amount / 100 : 0);
            $refundAmount += $this->refundLessonPaymentLogs($lessonPaymentLogs);

            $this->instructorEarning($lesson, $instructorFee, "cancel_lesson");
        } else if ($lesson->payment_by === Lesson::PAYMENT_BY['credit']) {

            list($refund, $refundAmount) = $this->refundCreditLesson($lesson, $instructorFee, $cancellationFee, $refundAmount, $refund);

            if (($lesson->additional_cost + $lesson->additional_tax) > 0 && $lesson->payment_intent_id) {
                $additionalCharges = $this->stripeService->refund($lesson->payment_intent_id);

                $refundAmount += ($additionalCharges ? $additionalCharges->amount / 100 : 0);
            }

            $refundAmount += $this->refundLessonPaymentLogs($lessonPaymentLogs);
        }

        $this->updateLessonCancellation($cancelled_lesson, $instructorFee, $drivisaFee, $cancellationFee, $refund, $additionalCharges, $refundAmount);

        $lesson->update(['status' => Lesson::STATUS['canceled']]);

        $lesson->trainee->user->notify(new InstructorCancelLessonTraineeNotification($lesson));
        $lesson->instructor->user->notify(new InstructorCancelLessonNotification($lesson));
    }

    private function getCancellationAmount($course, $courseUsedHistory, $cancellationFee = null)
    {
        $transaction = $course->purchase->transaction;

        $amount = $course->is_extra_credit_hours
            ? $this->calculateHourlyPrice($transaction->amount, $course->credit)
            : $this->calculateHourlyPrice($transaction->amount, $course->package->packageData->hours);

        $refundAmount = $amount * $courseUsedHistory->credit_used - ($cancellationFee ?? 0);

        return $transaction->amount - $refundAmount;
    }

    public function getInstructorLessonList($instructor, $take = 100)
    {
        $upcomingLessons = $instructor->lessons()
            ->whereDate('start_at', '>', today())
            ->orderByRaw('DATE(start_at)')
            ->orderBy('start_time', 'asc')
            ->take($take)->get();
        return [
            'today' => LessonInstructorTransformer::collection($instructor->lessons()->whereDate('start_at', today())->whereNotIn('status', [Lesson::STATUS['rescheduled']])->orderBy('start_time')->get()),
            'upcoming' => LessonInstructorTransformer::collection($upcomingLessons)
        ];
    }

    public function getTraineeLessonList($trainee)
    {
        $upcomingLessons = $trainee->lessons()
            ->whereNotIn('status', [Lesson::STATUS['rescheduled']])
            ->whereDate('start_at', '>', today())
            ->orderByRaw('DATE(start_at)')
            ->orderBy('start_time', 'asc')
            ->take(50)
            ->get();

        return [
            'today' => LessonTraineeTransformer::collection($trainee->lessons()->whereDate('start_at', today())->whereNotIn('status', [Lesson::STATUS['rescheduled']])->orderBy('start_time')->get()),
            'upcoming' => LessonTraineeTransformer::collection($upcomingLessons)
        ];
    }

    public function getTraineePastLessonList($trainee, $data)
    {
        $per_page = $data['per_page'] ?? 50;

        return $trainee->lessons()->where('start_at', "<", now())
            ->orderByRaw('DATE(start_at) DESC')
            ->orderBy('start_time')
            ->paginate($per_page);
    }

    public function getLastTrip($trainee)
    {
        return $trainee->lessons()->latest('ended_at')
            ->whereStatus(Lesson::STATUS['completed'])
            ->whereNull('trainee_note')
            ->whereNull('trainee_evaluation')
            ->first();
    }

    public function availableForReschedule($trainee)
    {
        return $trainee->lessons()
            ->whereStatus(Lesson::STATUS['reserved'])
            ->get();
    }

    /**
     * @param $lesson
     * @param $instructor
     */
    public function createTransfer($lesson, $instructor)
    {
        $lessonType = $lesson->lesson_type;
        $instructorId = $lesson->instructor->id;
        $netAmount = 0;

        if (in_array($lessonType, [Lesson::TYPE['car_rental'], Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
            if ($lessonType === Lesson::TYPE['g_test']) {
                $netAmount = Settings::get('instructor_g_test_fee');
            } elseif ($lessonType === Lesson::TYPE['g2_test']) {
                $netAmount = Settings::get('instructor_g2_test_fee');
            }
        } elseif ($lessonType === Lesson::TYPE['bde']) {
            $netAmount = Settings::get('instructor_bde_fee') * $lesson->duration;
        } else {
            $netAmount = Settings::get('instructor_driving_fee') * $lesson->duration;
        }

        $baseAmount = $netAmount + $lesson->additional_cost;

        $instructorEarning = InstructorEarning::create([
            'lesson_id' => $lesson->id,
            'instructor_id' => $instructorId,
            'type' => InstructorEarning::TYPE['lesson_complete'],
            'amount' => $netAmount,
            'additional_cost' => $lesson->additional_cost
        ]);

        if ($instructor?->user?->refer_id && !in_array($lessonType, [Lesson::TYPE['g_test'], Lesson::TYPE['g2_test']])) {
            $this->stripeService->sendReferralIncome($instructor, $lesson->duration);
        }

        $tax = $baseAmount * self::STATIC_TAX_VALUE;
        $totalAmount = $baseAmount + $tax;

        $instructorEarning->update([
            'tax' => $tax,
            'total_amount' => $totalAmount
        ]);

        $paymentTransferService = new PaymentTransferService();
        return $paymentTransferService->createTransfer($totalAmount, $instructor->stripeAccount);
    }

    /**
     * @param $trainee
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForLessonReschedule($trainee, $lesson): void
    {
        $eventVariables = [
            $trainee,
            $lesson
        ];

        event(new LessonReschedule(...$eventVariables));
    }

    public function reschedule($data, $trainee)
    {
        $oldLesson = $this->lessonRepository->find($data['lesson_id']);
        throw_if(!$oldLesson, new Exception('Lesson not found', Response::HTTP_UNPROCESSABLE_ENTITY));
        throw_unless($oldLesson->status === Lesson::STATUS['reserved'], new LessonStatusCanNotChangeException("Can't change status"));

        $workingHour = $this->workingHourRepository->find($data['working_hour_id']);
        throw_if(!$workingHour, new Exception('Working hour not found', Response::HTTP_UNPROCESSABLE_ENTITY));
        throw_if($workingHour->status !== WorkingHour::STATUS['available'], new Exception(trans('drivisa::drivisa.messages.deny_update_working_hour_unavailable')));

        $bookingService = app(BookingService::class);
        $bookingService->preventBookingMoreThanTwoHoursADay($trainee, Carbon::parse($workingHour->workingDay->date), $workingHour->duration, $oldLesson);

        $oldWorkingHour = $this->workingHourRepository->find($oldLesson->working_hour_id);

        $oldLesson->update([
            'status' => Lesson::STATUS['rescheduled'],
            'is_rescheduled' => 1,
            'reschedule_time' => now(),
        ]);

        $lesson = $this->createRescheduledLesson($oldLesson, $workingHour);

        // book new working hour
        $workingHour->update(['status' => WorkingHour::STATUS['unavailable']]);

        //release old working hour for availability
        $oldWorkingHour->update(['status' => WorkingHour::STATUS['available']]);

        //charge if reschedule within 24 hours
        if (isset($data['payment_method_id']) && $data['payment_method_id']) {
            $paymentIntent = $this->charge(
                $trainee,
                $data['payment_method_id'],
                $oldLesson,
                $oldLesson->instructor->stripeAccount->account_id
            );

            $lesson->update(['rescheduled_payment_id' => $paymentIntent->id]);
            $oldLesson->update(['rescheduled_payment_id' => $paymentIntent->id]);

            $transaction = $this->createTransactionHistory($paymentIntent);
            $this->createPurchaseHistory($transaction, $oldLesson, $trainee);

            $instructorAmount = self::IN_CAR_INSTRUCTOR_CANCEL_FEE * $lesson->duration;
            $instructorChargeAmount = $instructorAmount + ($instructorAmount * Settings::get('lesson_tax') / 100);

            $this->instructorEarning($oldLesson, $instructorChargeAmount, "reschedule_lesson");
        }

        if ($workingHour->workingDay->instructor_id !== $oldWorkingHour->workingDay->instructor_id) {
            $lesson->instructor->user->notify(new InstructorBookingReceviedNotification($lesson));
        }

        $lesson->trainee->user->notify(new TraineeLessonRescheduleNotification($lesson));
        $oldLesson->instructor->user->notify(new InstructorTraineeLessonRescheduledNotification($lesson, $workingHour, $oldWorkingHour));
        // $this->allEventVariablesThatNeedForLessonReschedule($trainee, $lesson);
    }

    private function createRescheduledLesson($oldLesson, $workingHour)
    {
        $start_date_time = $workingHour->workingDay->date . ' ' . $workingHour->open_at;
        $end_date_time = $workingHour->workingDay->date . ' ' . $workingHour->close_at;
        $start_time = $workingHour->open_at;

        return $this->lessonRepository->create([
            'no' => Carbon::now()->timestamp,
            'lesson_type' => $oldLesson->lesson_type,
            'start_at' => $start_date_time,
            'end_at' => $end_date_time,
            'start_time' => $start_time,
            'status' => Lesson::STATUS['reserved'],
            'started_at' => $oldLesson->started_at,
            'ended_at' => $oldLesson->ended_at,
            'is_request' => $oldLesson->is_request,
            'confirmed' => $oldLesson->confirmed,
            'cost' => $oldLesson->cost,
            'commission' => $oldLesson->commission,
            'net_amount' => $oldLesson->net_amount,
            'tax' => $oldLesson->tax,
            'additional_tax' => $oldLesson->additional_tax,
            'additional_cost' => $oldLesson->additional_cost,
            'additional_km' => $oldLesson->additional_km,
            'paid_at' => $oldLesson->paid_at,
            'pickup_point' => $oldLesson->pickup_point,
            'dropoff_point' => $oldLesson->dropoff_point,
            'instructor_id' => $workingHour->workingDay->instructor_id,
            'trainee_id' => $oldLesson->trainee_id,
            'created_by' => $oldLesson->created_by,
            'transaction_id' => $oldLesson->transaction_id,
            'payment_intent_id' => $oldLesson->payment_intent_id,
            'charge_id' => $oldLesson->charge_id,
            'working_hour_id' => $workingHour->id,
            'transfer_id' => $oldLesson->transfer_id,
            'payment_by' => $oldLesson->payment_by,
            'bde_number' => $oldLesson->bde_number,
            'rental_request_id' => $oldLesson->rental_request_id,
            'trainee_notification_id' => $oldLesson->trainee_notification_id,
            'instructor_notification_id' => $oldLesson->instructor_notification_id,
            'is_notification_sent' => $oldLesson->is_notification_sent,
            'notification_sent_at' => $oldLesson->notification_sent_at,
            'credit_use_histories_id' => $oldLesson->credit_use_histories_id,
            'is_bonus_credit' => $oldLesson->is_bonus_credit,
            'last_lesson_id' => $oldLesson->id
        ]);
    }

    private function charge($trainee, $payment_method_id, $lesson, $account_id)
    {
        $baseAmount = 25;
        $instructorBaseAmount = 20 + (20 * self::STATIC_TAX_VALUE);

        $start_at = Carbon::parse($lesson->start_at);
        $end_at = Carbon::parse($lesson->end_at);
        $duration = $start_at->diffInHours($end_at);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $chargeAmount = ($baseAmount * $duration);
        $chargeAmount = $chargeAmount + ($chargeAmount * (Settings::get('lesson_tax') / 100));

        return PaymentIntent::create([
            'customer' => $trainee->stripe_customer_id,
            'payment_method' => $payment_method_id,
            'amount' => $chargeAmount * 100,
            'currency' => 'CAD',
            'confirmation_method' => 'manual',
            'confirm' => true,
            'transfer_data' => [
                'amount' => ($instructorBaseAmount * $duration) * 100,
                'destination' => $account_id,
            ],
            'description' => "Payment made by $trainee->first_name $trainee->last_name for reschedule lesson whithin 24 hours and instructor will get $" . $instructorBaseAmount * $duration . " as reschedule compensation"
        ]);
    }

    private function createTransactionHistory($paymentIntent)
    {
        return $this->transactionService->create([
            'amount' => (float)($paymentIntent->amount / 100),
            'payment_intent_id' => $paymentIntent->id,
            'tx_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'charge_id' => $paymentIntent->charges->data[0]->id,
            'response' => $paymentIntent,
        ]);
    }

    private function createPurchaseHistory($transaction, $lesson, $trainee)
    {

        $purchase = new Purchase;

        $purchase->transaction_id = $transaction->id;
        $purchase->type = Purchase::TYPE['reschedule'];
        $purchase->trainee_id = $trainee->id;

        $lesson->purchases()->save($purchase);
    }

    public function getInstructorAvailability($lesson)
    {
        return $this->instructorRepository->find($lesson->instructor_id); // Instructor::find();
    }

    public function getProgress($lesson, $trainee)
    {
        $progress = json_decode($lesson->instructor_evaluation, true);
        return [
            'instructor' => $this->instructorRepository->find($lesson->instructor_id),
            'progress' => $progress
        ];
    }


    /**
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForCancelLessonByInstructor($lesson, $refundAmount, $additional_costs, $cancellationFee): void
    {
        $eventVariables = [
            $lesson,
            $refundAmount,
            $additional_costs,
            $cancellationFee
        ];

        event(new CancelLessonByInstructor(...$eventVariables));
    }

    /**
     * @param $lesson
     * @param $cancelled_lesson
     * @return void
     */
    public function cancelLessonByInstructor($lesson, &$cancelled_lesson): void
    {
        $refund = null;
        $additional_costs = 0;
        $cancellationFee = null;
        $refundAmount = 0;
        $lessonPaymentLogs = LessonPaymentLog::where('lesson_id', $lesson->id)->get();
        $course = CreditUseHistory::where('id', $lesson->credit_use_histories_id)->first()?->course;

        $courseData = [
            'user_id' => $lesson->trainee->user_id,
            'duration' => $lesson->duration,
            'previous_course_id' => $course?->id
        ];

        if ($lesson->lesson_type === Lesson::TYPE['bde']) {

            ($lesson->is_bonus_credit !== 0) ? $this->courseService->refundBonusBdeCredit($courseData) : $this->courseService->refundBdeCredit($courseData);

            $cancelled_lesson->update(['is_refunded' => 1]);
        } else if (!in_array($lesson->lesson_type, [Lesson::TYPE['bde'], Lesson::TYPE['g_test'], Lesson::TYPE['g2_test']])) {

            //1. check time of lesson is exceeded or not then refund money
            $roadTestCancellationFee = $this->getCancellationFeeFromAmount(self::ROAD_TEST_CANCELLATION_FEE);
            $isCancellationFeeApplied = $this->isLessonTimeExceed($lesson->start_at);
            $cancellationFee = $isCancellationFeeApplied ? $roadTestCancellationFee : 0;

            $additional_costs = $lesson->additional_cost + $lesson->additional_tax;

            if ($lesson->is_bonus_credit !== 0) {
                $this->courseService->refundBonusCredit($courseData);
            } else {
                $refundMethod = $lesson->cost > 0 ? 'refundCancelledCredit' : 'refundCredit';
                $this->courseService->$refundMethod($courseData);
            }

            if ($additional_costs > 0) {

                $refund = $this->stripeService->refund(
                    $lesson->payment_intent_id
                );

                $cancelled_lesson->update([
                    'refund_id' => $refund?->id,
                    'cancellation_fee' => $cancellationFee
                ]);

                $refundAmount += ($refund ? $refund->amount / 100 : 0);
            }
        } else {
            throw new Exception("Lesson Can't cancelled by you!");
        }

        $refundAmount += $this->refundLessonPaymentLogs($lessonPaymentLogs);

        $cancelled_lesson->update([
            'refund_amount' => $refundAmount
        ]);

        $this->allEventVariablesThatNeedForCancelLessonByInstructor($lesson, $refundAmount, $additional_costs, $cancellationFee);

        $lesson->trainee->user->notify(new InstructorCancelLessonTraineeNotification($lesson));
        $lesson->instructor->user->notify(new InstructorCancelLessonNotification($lesson));
    }

    /**
     * @param $lesson
     * @return void
     */
    private function whenCancelLessonWorkingHourRelease($lesson): void
    {
        if ($lesson->working_hour_id) {
            $workingHour = $this->workingHourRepository->find($lesson->working_hour_id);
            $open_at = Carbon::parse($workingHour->open_at);
            $close_at = Carbon::parse($workingHour->close_at);
            $duration = $open_at->diffInMinutes($close_at);

           if($duration > 120) {
               $workingHour->close_at = Carbon::parse($open_at)->addMinutes(120)->format('H:i');
           }
            $workingHour->status = WorkingHour::STATUS['available'];
            $workingHour->save();
        }
    }

    private function getInstructorRefundAmount(Lesson $lesson)
    {
        $in_car_fees = 20 + (20 * self::STATIC_TAX_VALUE);
        $road_test_fees = 50 + (50 * self::STATIC_TAX_VALUE);
        $bde_lesson_fee = 0;
        if ($lesson->lesson_type === Lesson::TYPE['driving']) {
            return $in_car_fees * $lesson->duration;
        } elseif ($lesson->lesson_type === Lesson::TYPE['bde']) {
            return $bde_lesson_fee;
        } else if ($lesson->lesson_type !== Lesson::TYPE['driving']) {
            return $road_test_fees;
        }
    }

    public function todayLessonsList($data)
    {
        if (!empty($data)) {
            return LessonTransformer::collection($this->searchLessons($data));
        }

        return LessonTransformer::collection(Lesson::whereDate('start_at', today())
            ->orderByRaw('DATE(start_at)')
            ->orderBy('start_time', 'asc')
            ->get());
    }

    public function createLessonByAdmin($user, $trainee, $data)
    {
        $lessonType = ($data['lessonType'] === "In Car Private Lesson") ? 1 : 2;
        $dateTime = Carbon::parse($data['dateTime']);
        $durationMinutes = ($data['duration'] === 1) ? 60 : 120;
        $startAt = $dateTime->format('Y-m-d H:i:s');
        $endAt = $dateTime->copy()->addMinutes($durationMinutes)->format('Y-m-d H:i:s');
        $startTime = $dateTime->format('H:i:s');
        $data['course_type'] = $data['courseType'];
        $instructor = $this->instructorRepository->find($data['instructor_id']);

        // Get conflicting lessons for trainee
        $traineeConflictingLessons = $this->getConflictingLessons($trainee, $startAt, $endAt);

        // Get conflicting lessons for instructor
        $instructorConflictingLessons = $this->getConflictingLessons($instructor, $startAt, $endAt);

        if ($traineeConflictingLessons->count() > 0 || $instructorConflictingLessons->count() > 0) {
            throw new Exception('Lesson time is conflicting', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // find course availability
        $courseAvailable = $this->getCourseAvailability($trainee->user, $data);
        if (!$courseAvailable) {
            throw new Exception(trans('drivisa::drivisa.messages.course_not_available'));
        }

        $lesson = $this->lessonRepository->create([
            'instructor_id' => $instructor->id,
            'trainee_id' => $trainee->id,
            'created_by' => $user->id,
            'no' => Carbon::now()->timestamp,
            'start_at' => $startAt,
            'start_time' => $startTime,
            'end_at' => $endAt,
            'started_at' => $startAt,
            'ended_at' => $endAt,
            'lesson_type' => $lessonType,
            'status' => 3,
            'bde_number' => $lessonType === 2 ? $this->getBdeNumber($trainee) : null,
            'is_bonus_credit' => 0,
            'transfer_id' => null
        ]);

        if ($data['lessonStatus'] === "completedPay") {
            $transfer = $this->createTransfer($lesson, $instructor);
            $lesson->update(['transfer_id' => $transfer->id]);
        }

        $workingHour = $this->createWorkingHour($lesson, Carbon::parse($startAt), Carbon::parse($endAt), $instructor, $trainee);
        $lesson->update(['working_hour_id' => $workingHour->id]);

        $courseAvailable->update(['status' => Course::STATUS['progress']]);

        if (in_array($courseAvailable->type, [Course::TYPE['Bonus'], Course::TYPE['Bonus_BDE']])) {
            $lesson->update(['is_bonus_credit' => 1]);
        }

        $creditUseHistory = CreditUseHistory::create([
            'course_id' => $courseAvailable->id,
            'lesson_id' => $lesson->id,
            'used_at' => now(),
            'credit_used' => (int)round(($data['duration'])),
        ]);

        $lesson->update(['credit_use_histories_id' => $creditUseHistory->id]);

        $totalUsedCredits = CreditUseHistory::where('course_id', $courseAvailable->id)->sum('credit_used');
        if ($courseAvailable->credit === (int)round($totalUsedCredits)) {
            $courseAvailable->update(['status' => Course::STATUS['completed']]);
        }
    }

    private function getCourseAvailability($user, $data)
    {
        $courses_ids = collect(DB::select("select dc.id as id, COALESCE(sum(dcuh.credit_used), 0) total_used, dc.credit as credit from drivisa__credit_use_histories dcuh
        right join drivisa__courses dc
        on dc.id = dcuh.course_id
        where dc.user_id = {$user->id}
        group by dc.id
        having total_used < credit;"))->pluck('id');

        return $user->courses()
            ->whereNotIn('status', [Course::STATUS['completed'], Course::STATUS['canceled']])
            ->whereIn('id', $courses_ids)
            ->with('package.packageData')
            ->when($data['course_type'] == "BDE", function ($query) {
                $query->whereIn('type', [Course::TYPE['BDE'], Course::TYPE['Refund_BDE'], Course::TYPE['Bonus_BDE']]);
            })
            ->when($data['course_type'] != "BDE", function ($query) {
                $query->whereNotIn('type', [Course::TYPE['BDE'], Course::TYPE['Refund_BDE'], Course::TYPE['Bonus_BDE']]);
            })
            ->first();
    }

    private function getBdeNumber($trainee)
    {
        $lessons = $trainee->lessons()->where('lesson_type', Lesson::TYPE['bde'])->get();
        $bde_lesson_number = 0;

        foreach ($lessons as $lesson) {
            $bde_lesson_number += $lesson->get_duration; // 2 + 2 + 2
        }

        return $bde_lesson_number + 1;
    }

    /**
     * @param $lesson
     * @param Carbon $start_at
     * @param Carbon $end_at
     */
    private function createWorkingHour($lesson, Carbon $start_at, Carbon $end_at, $instructor, $trainee)
    {
        $workingDay = $this->workingDayRepository->findByAttributes([
            'date' => Carbon::parse($start_at)->format('Y-m-d'),
            'instructor_id' => $lesson->instructor_id
        ]);

        if (!$workingDay) {
            $workingDay = $this->workingDayRepository->create([
                'date' => Carbon::parse($start_at)->format('Y-m-d'),
                'status' => WorkingDay::STATUS['available'],
                'instructor_id' => $lesson->instructor_id
            ]);
        }

        $openAt = $start_at->copy()->addMinutes(-15)->format('H:i');
        $closeAt = $end_at->copy()->addMinutes(15)->format('H:i');

        $workingHours = $this->workingHourRepository->allWithBuilder()
            ->where('working_day_id', $workingDay->id)
            ->whereNested(function ($query) use ($openAt, $closeAt) {
                $query->where(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '>', $openAt)->where('open_at', '<', $closeAt);
                });
                $query->orWhere(function ($query) use ($openAt, $closeAt) {
                    $query->where('close_at', '>', $openAt)->where('close_at', '<', $closeAt);
                });
                $query->orwhere(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '<=', $openAt)->where('close_at', '>=', $closeAt);
                });
            })
            ->get();

        foreach ($workingHours as $single_working_hours) {
            $single_working_hours->delete();
        }

        $point = $this->getPoint($instructor, $trainee);

        return $this->workingHourRepository->create([
            'working_day_id' => $workingDay->id,
            'open_at' => $start_at,
            'close_at' => $end_at,
            'point_id' => $point->id,
            'status' => 2
        ]);
    }

    private function getPoint($instructor, $trainee)
    {
        $latestLesson = $trainee?->lessons()->whereNotNull('ended_at')->where('instructor_id', $instructor->id)->latest('ended_at')->first();

        if ($latestLesson) {
            $workingHour = WorkingHour::find($latestLesson->working_hour_id);
            if ($workingHour) {
                return Point::find($workingHour->point_id);
            }
        }

        return $instructor->points()->where('is_active', true)->first();
    }

    private function getConflictingLessons($user, $startAt, $endAt)
    {
        return $user->lessons()
            ->where(function ($query) use ($startAt, $endAt) {
                $query->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt)
                    ->where(function ($query) use ($startAt, $endAt) {
                        $query->where('start_at', '!=', $startAt)
                            ->orWhere('end_at', '!=', $endAt);
                    });
            })->get();
    }

    public function instructorEarning($lesson, $amount, $type)
    {
        $tax = Settings::get('lesson_tax');
        $baseAmount = $amount / (1 + ($tax / 100));
        $taxAmount = $amount - $baseAmount;

        InstructorEarning::create([
            'lesson_id' => $lesson->id,
            'instructor_id' => $lesson->instructor->id,
            'type' => InstructorEarning::TYPE[$type],
            'amount' => $baseAmount,
            'tax' => $taxAmount,
            'total_amount' => $amount
        ]);
    }

    private function refundLessonPaymentLogs($lessonPaymentLogs)
    {
        $amount = 0;
        if ($lessonPaymentLogs->isNotEmpty()) {
            foreach ($lessonPaymentLogs as $lessonPaymentLog) {
                $refund = $this->stripeService->refund($lessonPaymentLog->payment_intent_id);
                $amount += ($refund->amount / 100);
            }
        }

        return $amount;
    }

    private function cancellationFeeAfertStartTime($lesson): array
    {
        $tax = Settings::get('lesson_tax') / 100;

        list($instructorFee, $drivisaFee) = $this->getLessonCancellationFee($lesson);

        $instructorFee = $instructorFee * (1 + $tax);
        $drivisaFee = $drivisaFee * (1 + $tax);

        return [$instructorFee, $drivisaFee, $instructorFee + $drivisaFee];
    }

    private function getLessonCancellationFee($lesson): array
    {
        $instructorFee = 0;
        $drivisaFee = 0;

        if (in_array($lesson->lesson_type, [Lesson::TYPE['car_rental'], Lesson::TYPE['g2_test'], Lesson::TYPE['g_test']])) {
            $instructorFee =  Settings::get('instructor_g_test_cancellation_fee_after_time');
            $drivisaFee = Settings::get('drivisa_g_test_cancellation_fee_after_time');
        } elseif (in_array($lesson->lesson_type, [Lesson::TYPE['bde'], Lesson::TYPE['driving']])) {
            $instructorFee =  Settings::get('instructor_driving_cancellation_fee_after_time') * $lesson->duration;
            $drivisaFee = Settings::get('drivisa_driving_cancellation_fee_after_time') * $lesson->duration;
        }

        return [$instructorFee, $drivisaFee];
    }

    private function updateLessonCancellation($cancelled_lesson, $instructorFee = null, $drivisaFee = null, $cancellationFee = 0,  $refund = null, $additionalCharges = null, $refundAmount = 0)
    {
        $refundId = $refund ? $refund->id : ($additionalCharges ? $additionalCharges->id : null);

        $cancelled_lesson->update([
            'refund_id' => $refundId,
            'instructor_fee' => $instructorFee,
            'drivisa_fee' => $drivisaFee,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount
        ]);
    }

    private function refundCreditLesson($lesson, $instructorFee, $cancellationFee, $refundAmount, $refund)
    {
        $courseUsedHistory = CreditUseHistory::where('id', $lesson->credit_use_histories_id)->first();
        if (!$courseUsedHistory) {
            throw new \Exception('Credit used history not found');
        }

        $course = $courseUsedHistory->course;
        if (!$course) {
            throw new \Exception('Course not found');
        }

        $course = $course->previous_course_id ? Course::find($course->previous_course_id) : $course;

        if ($course->payment_intent_id) {
            $totalAmount = $this->getCancellationAmount($course, $courseUsedHistory, $cancellationFee);

            $refund = $this->stripeService->refundUpdated(
                $course->payment_intent_id,
                $totalAmount,
                $lesson->instructor->stripeAccount->account_id,
                $instructorFee
            );

            $refundAmount += ($refund ? $refund->amount / 100 : 0);

            $this->instructorEarning($lesson, $instructorFee, "cancel_lesson");
        }

        return [$refund, $refundAmount];
    }
}
