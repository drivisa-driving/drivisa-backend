<?php

namespace Modules\Drivisa\Services;

use Exception;
use Carbon\Carbon;
use Modules\Drivisa\Entities\Discount;
use Modules\Drivisa\Entities\DiscountUser;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Modules\Drivisa\Entities\Car;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\Lesson;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Events\NewCarRentalBooked;
use Modules\Drivisa\Entities\InstructorEarning;
use Modules\Drivisa\Repositories\PointRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;
use Modules\Drivisa\Repositories\RentalRequestRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\Drivisa\Notifications\RoadTestRequestPaidNotification;
use Modules\Drivisa\Notifications\TraineeRoadTestRequestNotification;
use Modules\Drivisa\Notifications\TraineeRoadTestRequestPaidNotification;
use Modules\Drivisa\Notifications\InstructorCancelLessonTraineeNotification;
use Modules\Drivisa\Notifications\InstructorRoadTestRequestReceivedNotification;

class RentalRequestService
{
    private TraineeRepository $traineeRepository;
    private InstructorRepository $instructorRepository;
    private RentalRequestRepository $rentalRequestRepository;
    private PointRepository $pointRepository;
    private TransactionService $transactionService;
    private LessonRepository $lessonRepository;
    private PackageRepository $packageRepository;
    private LessonCancellationRepository $lessonCancellationRepository;
    private LessonCancellationService $lessonCancellationService;
    private StripeService $stripeService;
    private CourseService $courseService;
    private BookingService $bookingService;
    private WorkingDayRepository $workingDayRepository;
    private WorkingHourRepository $workingHourRepository;

    const EXPIRES_TIME_IN_HOURS_FOR_REQUEST = 24;
    const ROAD_TEST_RESCHEDULE_FEE = 60;
    const INSTRUCTOR_ROAD_TEST_RESCHEDULE_FEE = 50;
    const STATIC_TAX_VALUE = 0.13; # conversion of 13/100

    /**
     * @param TraineeRepository $traineeRepository
     * @param InstructorRepository $instructorRepository
     * @param RentalRequestRepository $rentalRequestRepository
     * @param PointRepository $pointRepository
     * @param TransactionService $transactionService
     * @param LessonRepository $lessonRepository
     * @param PackageRepository $packageRepository
     * @param LessonCancellationRepository $lessonCancellationRepository
     * @param LessonCancellationService $lessonCancellationService
     * @param StripeService $stripeService
     * @param CourseService $courseService
     * @param BookingService $bookingService
     * @param WorkingDayRepository $workingDayRepository
     * @param WorkingHourRepository $workingHourRepository
     */
    public function __construct(
        TraineeRepository            $traineeRepository,
        InstructorRepository         $instructorRepository,
        RentalRequestRepository      $rentalRequestRepository,
        PointRepository              $pointRepository,
        TransactionService           $transactionService,
        LessonRepository             $lessonRepository,
        PackageRepository            $packageRepository,
        LessonCancellationRepository $lessonCancellationRepository,
        LessonCancellationService    $lessonCancellationService,
        StripeService                $stripeService,
        CourseService                $courseService,
        BookingService               $bookingService,
        WorkingDayRepository         $workingDayRepository,
        WorkingHourRepository        $workingHourRepository
    )
    {

        $this->traineeRepository = $traineeRepository;
        $this->instructorRepository = $instructorRepository;
        $this->rentalRequestRepository = $rentalRequestRepository;
        $this->pointRepository = $pointRepository;
        $this->transactionService = $transactionService;
        $this->lessonRepository = $lessonRepository;
        $this->packageRepository = $packageRepository;
        $this->lessonCancellationRepository = $lessonCancellationRepository;
        $this->lessonCancellationService = $lessonCancellationService;
        $this->stripeService = $stripeService;
        $this->courseService = $courseService;
        $this->bookingService = $bookingService;
        $this->workingDayRepository = $workingDayRepository;
        $this->workingHourRepository = $workingHourRepository;
    }


    /**
     * @param $trainee
     * @param $data
     * @return void
     */
    public function register($trainee, $data)
    {
        $carbonDate = new Carbon($data['booking_date_time']);

        $this->bookingService->preventBookingMoreThanTwoHoursADay($trainee, $carbonDate, 2.5);

        $instructor = $this->instructorRepository->findByAttributes(['id' => $data['instructor_id']]);
        if (!$instructor) {
            throw new Exception("Instructor not found");
        }

        $lessons = $this->getConflictingCarRentals($carbonDate, $instructor);

        if ($lessons->count() > 0) {
            throw new Exception("Instructor is not available at this time. Please try to register request with a different time.");
        }

        $package = $this->packageRepository->find($data['package_id']);
        if (!$package) {
            throw new Exception("package not found");
        }

        $type = str_contains($package->name, '2') ? 2 : 1;

        $additional_charge = 0;
        $additional_tax = 0;
        $extra_distance = 0;
        $total_distance = 0;

        $instructor = $this->instructorRepository->findByAttributes(['id' => $data['instructor_id']]);

        $point = $this->pointRepository->findByAttributes(['id' => $data['ins_point_id']]);
        if (!$point) {
            throw new Exception("Point not found");
        }

        if ($point->instructor_id !== $instructor->id) {
            throw new AuthorizationException();
        }

        if($point->source_address == $data['pick_drop']['pick_address']){
            $data['pick_drop']['pick_lat'] = $point->source_latitude;
            $data['pick_drop']['pick_long'] = $point->source_longitude;
            $data['pick_drop']['drop_lat'] = $point->source_latitude;
            $data['pick_drop']['drop_long'] = $point->source_longitude;
        }

        $rentalRequest = $this->rentalRequestRepository->create([
            'trainee_id' => $trainee->id,
            'package_id' => $data['package_id'],
            'location' => $data['location'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'booking_date' => $carbonDate,
            'booking_time' => $carbonDate,
            'status' => RentalRequest::STATUS['registered'],
            'type' => $type,
            'instructor_id' => $data['instructor_id']
        ]);

        $start_at = Carbon::parse($carbonDate)->subMinutes(90);
        $end_at = Carbon::parse($carbonDate)->addMinutes(60);

        $workingHour = $this->scheduleBlockingSystem($rentalRequest, $start_at, $end_at, $point);

        // set lesson_id/working_hour_id to data
        $data['lesson_id'] = $workingHour->id;

        list(
            $data,
            $pick_point,
            $drop_point,
            $additional_charge,
            $additional_tax,
            $extra_distance,
            $total_distance
            ) = $this->bookingService->getAdditionalCostAndPoint(
            $data,
            $point,
            $additional_charge,
            $additional_tax,
            $extra_distance,
            $total_distance
        );

        $rentalRequest->update([
            'pickup_point' => $pick_point,
            'dropoff_point' => $drop_point,
            'additional_tax' => $additional_tax,
            'additional_cost' => $additional_charge,
            'additional_km' => $extra_distance,
            'total_distance' => $total_distance,
        ]);

        $this->sendCarRentalRequestToRecentInstructor($rentalRequest, $data['instructor_id']);
        $rentalRequest->trainee->user->notify(new TraineeRoadTestRequestNotification($rentalRequest, $instructor));

        $instructor->user->notify(new InstructorRoadTestRequestReceivedNotification($rentalRequest));
    }

    private function scheduleBlockingSystem($rentalRequest, $start_at, $end_at, $point)
    {
        // 1. check schedule is available or not
        // return  -> 1. if working day not available
        // 2. if working hours not available
        // 3. if working hours available

        // 2. determine that schedule 2.1. available or 2.2 not added or 2.3 already booked
        // 2.1 just make it unavailable
        // 2.2 add new timeline and make it unavailable
        // 2.3 do nothing
        if ($this->checkScheduleAvailability($rentalRequest, $start_at, $end_at) == 1) {
            $workingDay = $this->createWorkingDay($rentalRequest);
            return $this->createUnAvailableHour($rentalRequest, $workingDay, $point);
        } else if ($this->checkScheduleAvailability($rentalRequest, $start_at, $end_at) == 2) {
            $workingDay = $this->workingDayRepository->findByAttributes([
                'date' => $start_at->format('Y-m-d'),
                'instructor_id' => $rentalRequest->instructor_id
            ]);
            return $this->createUnAvailableHour($rentalRequest, $workingDay, $point);
        } else {
            $workingDay = $this->workingDayRepository->findByAttributes([
                'date' => $start_at->format('Y-m-d'),
                'instructor_id' => $rentalRequest->instructor_id
            ]);
            $workingHours = $this->getWorkingHours($start_at, $end_at, $workingDay);

            $working_hour = $workingHours->count() > 0 ? $workingHours->first() : null;

            foreach ($workingHours as $workingHour) {
                if ($workingHour->status == WorkingHour::STATUS['available']) {
                    $workingHour->status = WorkingHour::STATUS['unavailable'];
                    $workingHour->save();
                }
            }

            return $working_hour;
        }
    }

    private function checkScheduleAvailability($rentalRequest, $start_at, $end_at): int
    {
        $workingDay = $this->workingDayRepository->findByAttributes(
            [
                'date' => $start_at->format('Y-m-d'),
                'instructor_id' => $rentalRequest->instructor_id
            ]
        );
        if (!$workingDay) return 1;

        $workingHours = $this->getWorkingHours($start_at, $end_at, $workingDay);
        return $workingHours->count() > 0 ? 3 : 2;
    }

    private function createWorkingDay(RentalRequest $rentalRequest)
    {
        return $this->workingDayRepository->create([
            'date' => $rentalRequest->booking_date,
            'status' => WorkingDay::STATUS['available'],
            'instructor_id' => $rentalRequest->instructor_id
        ]);
    }

    private function createUnAvailableHour(RentalRequest $rentalRequest, WorkingDay $workingDay, Point $point)
    {
        $start_at = Carbon::parse($rentalRequest->booking_time)->subMinutes(90);
        $end_at = Carbon::parse($rentalRequest->booking_time)->addMinutes(60);

        return $this->workingHourRepository->create([
            'working_day_id' => $workingDay->id,
            'open_at' => $start_at,
            'close_at' => $end_at,
            'point_id' => $point->id,
            'status' => 2
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

    public function reschedule($trainee, $data)
    {
        $rentalRequest = $this->rentalRequestRepository->find($data['id']);
        $carbonDate = new Carbon($data['booking_date_time']);

        $this->bookingService->preventBookingMoreThanTwoHoursADay($trainee, $carbonDate, 2.5);

        $payment_intent_id = NULL;
        $totalCost = self::ROAD_TEST_RESCHEDULE_FEE + (self::ROAD_TEST_RESCHEDULE_FEE * self::STATIC_TAX_VALUE);

        if ($data['payment_method_id']) {

            $description = "Payment made by $trainee->first_name $trainee->last_name for reschedule " . $rentalRequest->type === 1 ? "G Test" : "G2 Test" . " within 24 hours";

            $paymentIntent = $this->SavePaymentIntent($data['payment_method_id'], $totalCost, $description);
            $this->createTransactionHistory($paymentIntent);

            $payment_intent_id = $paymentIntent->id;
        }

        $rentalRequestReschedule = $this->rentalRequestRepository->create([
            'trainee_id' => $trainee->id,
            'package_id' => $rentalRequest['package_id'],
            'location' => $rentalRequest['location'],
            'latitude' => $rentalRequest['latitude'],
            'longitude' => $rentalRequest['longitude'],
            'purchase_id' => $rentalRequest['purchase_id'],
            'instructor_id' => $rentalRequest['instructor_id'],
            'booking_date' => $carbonDate,
            'booking_time' => $carbonDate,
            'status' => RentalRequest::STATUS['registered'],
            'type' => $rentalRequest['type'],
            'pickup_point' => $rentalRequest['pickup_point'],
            'dropoff_point' => $rentalRequest['dropoff_point'],
            'additional_tax' => $rentalRequest['additional_tax'],
            'additional_cost' => $rentalRequest['additional_cost'],
            'additional_km' => $rentalRequest['additional_km'],
            'total_distance' => $rentalRequest['total_distance'],
            'is_reschedule_request' => 1,
            'last_request_id' => $data['id'],
            'lesson_id' => $rentalRequest['lesson_id'],
            'reschedule_payment_intent_id' => $payment_intent_id,
        ]);

        $start_at = Carbon::parse($carbonDate)->subMinutes(90);
        $end_at = Carbon::parse($carbonDate)->addMinutes(60);

        $lesson = Lesson::find($rentalRequest['lesson_id']);
        $workingHour = WorkingHour::find($lesson->working_hour_id);
        if (!$workingHour) {
            throw new Exception(trans('drivisa::drivisa.messages.working_hour_unavailable'));
        }

        $newWorkingHour = $this->scheduleBlockingSystem($rentalRequest, $start_at, $end_at, $workingHour->point);

        $this->sendCarRentalRequestToRecentInstructor($rentalRequestReschedule, $rentalRequest['instructor_id']);
        $instructor = $this->instructorRepository->findByAttributes(['id' => $rentalRequestReschedule['instructor_id']]);

        $trainee->user->notify(new TraineeRoadTestRequestNotification($rentalRequestReschedule, $instructor));

        $instructor->user->notify(new InstructorRoadTestRequestReceivedNotification($rentalRequestReschedule));
    }


    /**
     * @param $rentalRequest
     * @param $instructor_id
     * @return void
     */
    private function sendCarRentalRequestToRecentInstructor($rentalRequest, $instructor_id)
    {
        DB::table('drivisa__instructor_rental_request')
            ->insert([
                'instructor_id' => $instructor_id,
                'rental_request_id' => $rentalRequest->id
            ]);
    }

    /**
     * @throws \Throwable
     */
    private function sendCarRentalRequestToInstructors($rentalRequest)
    {
        throw_if(count($this->getNearestInstructor($rentalRequest)) === 0, new NotFoundHttpException('Instructor Not Found In your location'));

        foreach ($this->getNearestInstructor($rentalRequest) as $instructor) {
            DB::table('drivisa__instructor_rental_request')
                ->insert([
                    'instructor_id' => $instructor->id,
                    'rental_request_id' => $rentalRequest->id
                ]);
        }
    }

    private function getNearestInstructor($rentalRequest)
    {
        $nearestPoints = $this->getNearestPoints($rentalRequest);

        return $this->instructorRepository
            ->query()
            ->whereHas('points', function ($query) use ($nearestPoints) {
                $query->whereIn('id', $nearestPoints);
            })
            ->get();
    }

    /**
     * @param $rentalRequest
     * @return mixed
     */
    private function getNearestPoints($rentalRequest)
    {
        return $this->pointRepository->findNearestPoints(
            $rentalRequest->latitude,
            $rentalRequest->longitude
        )->pluck('id')->toArray();
    }

    public function getRequests($trainee, $data)
    {
        $per_page = $data['per_page'] ?? 50;

        return $trainee->rentalRequests()
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->paginate($per_page);
    }

    /**
     * @param $rentalRequest
     * @param $totalCost
     * @param $transaction
     * @return void
     */
    private function allEventVariablesThatNeedForBuyCarRental($rentalRequest, $packageData, $tax, $totalCost,$discount_amount): void
    {
        $eventVariables = [
            $rentalRequest,
            $packageData,
            $tax,
            $totalCost,
            $discount_amount
        ];

        event(new NewCarRentalBooked(...$eventVariables));
    }

    public function paid($rentalRequest, $data)
    {
        $packageData = $rentalRequest->package->packageData;
        $charge = $this->getCharge($rentalRequest);

        $discount = Discount::find($data['discount_id']);
        $discount_amount = 0;
        if ($discount) {
            $discount_id = $discount->id;
            $discount_main = $discount->discount_amount;
            $discount_amount = $discount->discount_amount;
            $discount_type = $discount->type;
            if ($discount->type == 'percent') {
                $discount_amount = ($discount_amount / 100) * $charge;
            }
        }
        $discountPrice = $charge - $discount_amount;
        $cost = $discountPrice + $this->getTax($discountPrice);
        $totalCost = $cost
            + $rentalRequest->additional_tax
            + $rentalRequest->additional_cost;

        $trainee = $rentalRequest->trainee;
        $description = "Payment made by $trainee->first_name $trainee->last_name for booking " . $rentalRequest->type === 1 ? "G Test" : "G2 Test";

        $paymentIntent = $this->savePaymentIntent(
            $data['payment_id'],
            $totalCost,
            $description
        );

        $transaction = $this->getTransaction($paymentIntent);

        //create purchase history
        $purchase = $this->createPurchaseHistory($transaction, $rentalRequest);

        $this->updateCarRentalRequest($rentalRequest, $purchase);

        // create lesson

        $lesson_id = $this->createLesson($rentalRequest, $cost, $packageData, $paymentIntent, $discount_amount);

        if ($discount) {
            DiscountUser::create(
                [
                    'user_id' => $trainee->id,
                    'discount_id' => $discount->id,
                    'discount_amount' => $discount_main,
                    'discount_type' => $discount_type,
                    'main_amount' => $charge,
                    'total_discount' => $discount_amount,
                    'after_discount' => $discountPrice,
                    'discount_used_name' => $rentalRequest->type === 1 ? "G Test" : "G2 Test",
                    'type' => $rentalRequest->type === 1 ? "g_test" : "g2_test",
                    'type_id' => $lesson_id,
                ]);
        }
    }

    private function savePaymentIntent($payment_method_id, $totalCost, $description = null)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'payment_method' => $payment_method_id,
            'amount' => $totalCost * 100,
            'currency' => 'CAD',
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => $description
        ]);
        return $paymentIntent;
    }

    private function getCharge($rentalRequest)
    {

        return $base_price = $rentalRequest->package->packageData->discount_price;

        //  return $base_price + $this->getTax($base_price);
    }

    private function getTax($base_price)
    {
        $tax = Settings::get('lesson_tax');
        return $base_price * ($tax / 100);
    }

    /**
     * @param PaymentIntent $paymentIntent
     * @return mixed
     */
    private function getTransaction(PaymentIntent $paymentIntent)
    {
        $transaction = $this->transactionService->create([
            'amount' => (float)($paymentIntent->amount / 100),
            'payment_intent_id' => $paymentIntent->id,
            'tx_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'charge_id' => $paymentIntent->charges->data[0]->id,
            'response' => $paymentIntent,
        ]);
        return $transaction;
    }

    /**
     * @param $transaction
     * @param $rentalRequest
     * @return void
     */
    private function createPurchaseHistory($transaction, $rentalRequest): Purchase
    {
        $testType = $rentalRequest->package->name;

        $purchaseType = Purchase::TYPE['g2_test'];

        if (stristr($testType, 'g road test')) {
            $purchaseType = Purchase::TYPE['g_test'];
        }

        $purchaseHistory = new Purchase;
        $purchaseHistory->transaction_id = $transaction->id;
        $purchaseHistory->type = $purchaseType;
        $purchaseHistory->trainee_id = $rentalRequest->trainee_id;

        $rentalRequest->purchases()->save($purchaseHistory);

        return $purchaseHistory;
    }

    /**
     * @param $rentalRequest
     * @param Purchase $purchase
     * @return void
     */
    private function updateCarRentalRequest($rentalRequest, Purchase $purchase): void
    {
        $rentalRequest->status = RentalRequest::STATUS['paid'];
        $rentalRequest->purchase_id = $purchase->id;
        $rentalRequest->save();
    }

    public function getAvailableRequests($instructor)
    {
        $status = RentalRequest::STATUS['registered'];
        $selected_id = DB::select('SELECT drr.id FROM drivisa__rental_requests drr
                                        INNER JOIN drivisa__instructor_rental_request dirr
                                        ON dirr.rental_request_id = drr.id
                                        WHERE dirr.instructor_id = ' . $instructor->id . ' AND drr.status = ' .
            $status . '');

        return RentalRequest::whereIn('id', collect($selected_id)->pluck('id')->toArray())
            //            ->whereRaw("DATE_ADD(created_at, interval +1 day) >= NOW()")
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();
    }

    public function getAcceptedRequests($instructor, $data)
    {
        $per_page = $data['per_page'] ?? 50;

        return RentalRequest::where('instructor_id', $instructor->id)
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->paginate($per_page);
    }

    /**
     * @param $rentalRequest
     * @param $totalCost
     * @param $packageData
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function createLesson($rentalRequest, $totalCost, $packageData, PaymentIntent $paymentIntent, $discount_amount = 0)
    {
        $date = $rentalRequest->booking_date->format('Y-m-d');
        $time = $rentalRequest->booking_time->format('H:i:s');

        $start_at = Carbon::parse($date . " " . $time)->subMinutes(90);
        $end_at = Carbon::parse($date . " " . $time)->addMinutes(60);
        $start_time = Carbon::parse($date . " " . $time)->subMinutes(90)->format('H:i:s');

        $tax = $this->getTax($packageData->discount_price - $discount_amount);

        $workingHour = $this->createWorkingHour($rentalRequest, $start_at, $end_at);

        $instructor = $this->instructorRepository->findByAttributes(['id' => $rentalRequest->instructor_id]);

        $lesson = $this->lessonRepository->create([
            'instructor_id' => $rentalRequest->instructor_id,
            'lesson_type' => $rentalRequest->type == 2 ? Lesson::TYPE['g2_test'] : Lesson::TYPE['g_test'],
            'trainee_id' => $rentalRequest->trainee_id,
            'created_by' => $rentalRequest->trainee->user_id,
            'no' => Carbon::now()->timestamp,
            'start_at' => $start_at,
            'start_time' => $start_time,
            'end_at' => $end_at,
            'cost' => $packageData->discount_price,
            'pickup_point' => $rentalRequest->pickup_point,
            'dropoff_point' => $rentalRequest->dropoff_point,
            'commission' => $packageData->drivisa,
            'tax' => $tax,
            'net_amount' => $totalCost - $packageData->drivisa,
            'transaction_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'payment_intent_id' => $paymentIntent->id,
            'charge_id' => $paymentIntent->charges->data[0]->id,
            'payment_by' => Lesson::PAYMENT_BY['stripe'],
            'additional_tax' => $rentalRequest->additional_tax,
            'additional_cost' => $rentalRequest->additional_cost,
            'additional_km' => $rentalRequest->additional_km,
            'rental_request_id' => $rentalRequest->id,
            'working_hour_id' => $workingHour->id
        ]);
        $rentalRequest->lesson_id = $lesson->id;
        $rentalRequest->save();

        $instructor->user->notify(new RoadTestRequestPaidNotification($rentalRequest, $lesson));
        $rentalRequest->trainee->user->notify(new TraineeRoadTestRequestPaidNotification($rentalRequest, $lesson));

        $this->allEventVariablesThatNeedForBuyCarRental($rentalRequest, $packageData, $tax, $totalCost,$discount_amount);

        return $lesson->id;
    }

    /**
     * @param $rentalRequest
     * @param $lesson
     * @return void
     */
    public function createRescheduleLesson($rentalRequest, $lesson): void
    {
        $date = $rentalRequest->booking_date->format('Y-m-d');
        $time = $rentalRequest->booking_time->format('H:i:s');

        $start_at = Carbon::parse($date . " " . $time)->subMinutes(90);
        $end_at = Carbon::parse($date . " " . $time)->addMinutes(60);
        $start_time = Carbon::parse($date . " " . $time)->subMinutes(90)->format('H:i:s');

        $workingHour = $this->createWorkingHour($rentalRequest, $start_at, $end_at);

        $instructor = $this->instructorRepository->findByAttributes(['id' => $rentalRequest->instructor_id]);

        if ($rentalRequest->reschedule_payment_intent_id) {
            $this->createInstructorTransfer($rentalRequest, $instructor, $lesson);
        }

        $newLesson = $this->lessonRepository->create([
            'instructor_id' => $rentalRequest->instructor_id,
            'lesson_type' => $rentalRequest->type == 2 ? Lesson::TYPE['g2_test'] : Lesson::TYPE['g_test'],
            'trainee_id' => $rentalRequest->trainee_id,
            'created_by' => $rentalRequest->trainee->user_id,
            'no' => Carbon::now()->timestamp,
            'start_at' => $start_at,
            'start_time' => $start_time,
            'end_at' => $end_at,
            'cost' => $lesson->cost,
            'pickup_point' => $rentalRequest->pickup_point,
            'dropoff_point' => $rentalRequest->dropoff_point,
            'commission' => $lesson->commission,
            'tax' => $lesson->tax,
            'net_amount' => $lesson->net_amount,
            'transaction_id' => $lesson->transaction_id,
            'payment_intent_id' => $lesson->payment_intent_id,
            'charge_id' => $lesson->charge_id,
            'payment_by' => $lesson->payment_by,
            'additional_tax' => $rentalRequest->additional_tax,
            'additional_cost' => $rentalRequest->additional_cost,
            'additional_km' => $rentalRequest->additional_km,
            'rental_request_id' => $rentalRequest->id,
            'working_hour_id' => $workingHour->id,
            'rescheduled_payment_id' => $rentalRequest->reschedule_payment_intent_id,
            'last_lesson_id' => $lesson->id
        ]);

        $rentalRequest->lesson_id = $newLesson->id;
        $rentalRequest->save();
    }

    public function getConflictingLessons($instructor, $rentalRequest)
    {

        $date = $rentalRequest->booking_date->format('Y-m-d');
        $time = $rentalRequest->booking_time->format('H:i:s');

        $bookingTime = Carbon::parse($date . " " . $time);

        $dateTimeFrom = $bookingTime->copy()->subMinutes(105);
        $dateTimeTo = $bookingTime->copy()->addMinutes(75);

        return $instructor
            ->lessons()
            ->whereStatus(Lesson::STATUS['reserved'])
            ->where(function ($query) use ($dateTimeFrom, $dateTimeTo) {
                $query->where('end_at', '>', $dateTimeFrom)
                    ->where('start_at', '<', $dateTimeTo)
                    ->orWhere(function ($query) use ($dateTimeFrom, $dateTimeTo) {
                        $query->where('start_at', '<', $dateTimeFrom)
                            ->where('end_at', '>', $dateTimeTo);
                    });
            })
            ->get();
    }

    public function cancelLessons($lesson_ids)
    {
        if ($lesson_ids == null || count($lesson_ids) == 0) return;
        $lessons = $this->lessonRepository->whereIn('id', $lesson_ids)->get();

        foreach ($lessons as $lesson) {
            $lesson->status = Lesson::STATUS['canceled'];
            $lesson->save();

            //            if ($lesson->payment_intent_id && $lesson->payment_by == Lesson::PAYMENT_BY['stripe']) {
            //                $this->stripeService->refund($lesson->payment_intent_id);
            //            } else {
            //                $this->courseService->refundCredit([
            //                    'user_id' => $lesson->trainee->user_id,
            //                    'duration' => $lesson->duration
            //                ]);
            //            }

            $lesson_cancellation = $this->lessonCancellationService->cancel($lesson, [
                'cancel_by' => 'Instructor',
                'reason' => "Instructor Booked for car rental service"
            ]);

            $lessonService = app(LessonService::class);
            $lessonService->cancelLessonByInstructor($lesson, $lesson_cancellation);


            $lesson->trainee->user->notify(new InstructorCancelLessonTraineeNotification($lesson));
        }
    }

    /**
     * @param $rentalRequest
     * @param Carbon $start_at
     * @param Carbon $end_at
     */
    private function createWorkingHour($rentalRequest, Carbon $start_at, Carbon $end_at)
    {
        $workingDay = $this->workingDayRepository->findByAttributes([
            'date' => $rentalRequest->booking_date->format('Y-m-d'),
            'instructor_id' => $rentalRequest->instructor_id
        ]);

        if (!$workingDay) {
            $workingDay = $this->workingDayRepository->create([
                'date' => $rentalRequest->booking_date->format('Y-m-d'),
                'status' => WorkingDay::STATUS['available'],
                'instructor_id' => $rentalRequest->instructor_id
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
        return $this->workingHourRepository->create([
            'working_day_id' => $workingDay->id,
            'open_at' => $start_at,
            'close_at' => $end_at,
            'point_id' => $rentalRequest->acceptedBy?->activePoint?->id,
            'status' => 2
        ]);
    }

    /**
     * @param $start_at
     * @param $end_at
     * @param $workingDay
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getWorkingHours($start_at, $end_at, $workingDay): array|\Illuminate\Database\Eloquent\Collection
    {
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
        return $workingHours;
    }

    public function refundRoadTestRescheduleFee($rentalRequest)
    {
        $intent_id = $rentalRequest->reschedule_payment_intent_id;
        $refundAmount = (self::ROAD_TEST_RESCHEDULE_FEE + (self::ROAD_TEST_RESCHEDULE_FEE * self::STATIC_TAX_VALUE)) * 100;

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $intent = $stripe->paymentIntents->retrieve($intent_id);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $refundObject = [
            'payment_intent' => $intent->id,
            'amount' => (int)$refundAmount
        ];
        Refund::create($refundObject);
    }

    public function releaseOldWorkingHour($rentalRequest)
    {
        // Calculate start time and date of rental request
        $start_at = Carbon::parse($rentalRequest->booking_time)->subMinutes(90);
        $end_at = Carbon::parse($rentalRequest->booking_time)->addMinutes(60);
        $date = Carbon::parse($rentalRequest->booking_date)->format('Y-m-d');

        // find workingDay
        $workingDay = $this->workingDayRepository->findByAttributes([
            'date' => $date,
            'instructor_id' => $rentalRequest->instructor_id
        ]);

        $workingHours = $this->getWorkingHours($start_at, $end_at, $workingDay);

        // 1. Get all working hours
        // 2. Check how many hours are available and conflicting
        // 3. if any hour conflicting to booked working hours then set them as unavailable
        // and set booked hours to available
        foreach ($workingHours as $workingHour) {
            if ($workingHour->status == WorkingHour::STATUS['unavailable']) {
                $workingHour->status = WorkingHour::STATUS['available'];
                $workingHour->save();
            }
        }
    }

    private function getConflictingCarRentals($carbonDate, $instructor)
    {
        $date = Carbon::parse($carbonDate)->format('Y-m-d');
        $time = Carbon::parse($carbonDate)->format('H:i:s');
        $bookingTime = Carbon::parse($date . " " . $time);

        $dateTimeFrom = $bookingTime->copy()->subMinutes(105);
        $dateTimeTo = $bookingTime->copy()->addMinutes(75);

        return $instructor
            ->lessons()
            ->whereIn('lesson_type', [Lesson::TYPE['g_test'], Lesson::TYPE['g2_test']])
            ->whereStatus(Lesson::STATUS['reserved'])
            ->where(function ($query) use ($dateTimeFrom, $dateTimeTo) {
                $query->whereBetween('start_at', [$dateTimeFrom, $dateTimeTo])
                    ->orWhereBetween('end_at', [$dateTimeFrom, $dateTimeTo]);
            })
            ->get();
    }

    public function createInstructorTransfer($rentalRequest, $instructor, $lesson)
    {
        $instructorRescheduleFee = self::INSTRUCTOR_ROAD_TEST_RESCHEDULE_FEE;
        $tax = self::INSTRUCTOR_ROAD_TEST_RESCHEDULE_FEE * self::STATIC_TAX_VALUE;
        $totalAmount = $instructorRescheduleFee + $tax;

        $paymentTransferService = new PaymentTransferService();
        $paymentTransferService->createTransfer($totalAmount, $instructor->stripeAccount);

        InstructorEarning::create([
            'lesson_id' => $lesson->id,
            'instructor_id' => $lesson->instructor->id,
            'type' => InstructorEarning::TYPE['reschedule_lesson'],
            'amount' => $instructorRescheduleFee,
            'tax' => $tax,
            'total_amount' => $totalAmount
        ]);
    }
}
