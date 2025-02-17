<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Car;
use Modules\Drivisa\Entities\Discount;
use Modules\Drivisa\Entities\DiscountUser;
use Stripe;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Stripe\Refund;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Package;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Events\NewBuyPackage;
use Modules\Drivisa\Events\NewLessonBooked;
use Modules\Drivisa\Entities\CreditUseHistory;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;
use Modules\Drivisa\Repositories\StripeAccountRepository;
use Modules\Drivisa\Transformers\WorkingHourWithCostTransformer;
use Modules\Drivisa\Notifications\TraineeLessonBookedNotification;
use Modules\Drivisa\Notifications\InstructorBookingReceviedNotification;

class BookingService
{
    const LESSON_COST_2_HOUR = 115;
    const GO_GO_SERVICE_URL = "https://elearning.gogodriving.com/local/enrol_api/req.php";
    const GO_GO_SERVICE_API_KEY = "f046c64b20d28b98e10b9ea6d54b50de";

    /**
     * @param LessonRepository $lessonRepository
     * @param WorkingHourRepository $workingHourRepository
     * @param StripeAccountRepository $stripeAccountRepository
     * @param CourseRepository $courseRepository
     * @param PurchaseService $purchaseService
     * @param TransactionService $transactionService
     * @param CourseService $courseService
     */
    public function __construct(
        private LessonRepository        $lessonRepository,
        private WorkingHourRepository   $workingHourRepository,
        private StripeAccountRepository $stripeAccountRepository,
        private CourseRepository        $courseRepository,
        private PurchaseService         $purchaseService,
        private TransactionService      $transactionService,
        private CourseService           $courseService
    ) {
    }

    public function bookingLesson($trainee, $data)
    {
        $id = $data['lesson_id'];
        $workingHour = $this->workingHourRepository->find($id);
        if ($workingHour->status == WorkingHour::STATUS['available']) {
            $workingHour->update(['status' => WorkingHour::STATUS['unavailable']]);
            $point = $workingHour->point;
            $workingDay = $workingHour->workingDay;
            if ($workingDay->status == WorkingDay::STATUS['available']) {
                $this->preventBookingMoreThanTwoHoursADay($trainee, Carbon::parse($workingDay->date), $workingHour->duration);
                $this->bookLesson($workingDay, $workingHour, $data, $point, $trainee);
            } else {
                throw new Exception(trans('drivisa::drivisa.messages.working_day_unavailable'));
            }
        } else {
            throw new Exception(trans('drivisa::drivisa.messages.working_hour_unavailable'));
        }
    }

    public function bookingLessonByCourse($trainee, $data)
    {
        $id = $data['lesson_id'];
        $workingHour = $this->workingHourRepository->find($id);
        $user = $trainee->user;

        $courses_ids = collect(DB::select("select dc.id as id, COALESCE(sum(dcuh.credit_used), 0) total_used, dc.credit as credit from drivisa__credit_use_histories dcuh
                                                    right join drivisa__courses dc
                                                    on dc.id = dcuh.course_id
                                                    where dc.user_id = {$user->id}
                                                    group by dc.id
                                                    having total_used < credit;"))->pluck('id');


        $course_available = $user->courses()
            ->whereNotIn('status', [Course::STATUS['completed'], Course::STATUS['canceled']])
            ->whereIn('id', $courses_ids)
            ->with('package.packageData')
            ->when($data['bde_lesson'] == true, function ($query) {
                $query->whereIn('type', [Course::TYPE['BDE'], Course::TYPE['Refund_BDE'], Course::TYPE['Bonus_BDE']]);
            })
            ->when($data['bde_lesson'] == false, function ($query) {
                $query->whereNotIn('type', [Course::TYPE['BDE'], Course::TYPE['Refund_BDE'], Course::TYPE['Bonus_BDE']]);
            })
            ->first();

        if (!$course_available) {
            throw new Exception(trans('drivisa::drivisa.messages.course_not_available'));
        }


        $package = $course_available->package;

        if ($workingHour->status == WorkingHour::STATUS['available']) {
            $workingHour->update(['status' => WorkingHour::STATUS['unavailable']]);
            $point = $workingHour->point;
            $workingDay = $workingHour->workingDay;
            if ($workingDay->status == WorkingDay::STATUS['available']) {

                $this->preventBookingMoreThanTwoHoursADay($trainee, Carbon::parse($workingDay->date), $workingHour->duration);

                $bookedLesson = $this->bookLesson($workingDay, $workingHour, $data, $point, $trainee, true);

                $course_available->status = Course::STATUS['progress'];
                $course_available->save();

                if ($course_available->type === Course::TYPE['Bonus'] || $course_available->type === Course::TYPE['Bonus_BDE']) {
                    $bookedLesson['lesson']->is_bonus_credit = 1;
                    $bookedLesson['lesson']->save();
                }

                $creditUseHistory =  CreditUseHistory::create([
                    'course_id' => $course_available->id,
                    'lesson_id' => $bookedLesson['lesson']->id,
                    'used_at' => now(),
                    'credit_used' => (int)round(($bookedLesson['duration'] / 60)),
                ]);

                $bookedLesson['lesson']->credit_use_histories_id = $creditUseHistory->id;
                $bookedLesson['lesson']->save();

                $totalUsedCredits = CreditUseHistory::where('course_id', $course_available->id)->sum('credit_used');
                if ($course_available->credit === (int)round($totalUsedCredits)) {
                    $course_available->status = Course::STATUS['completed'];
                    $course_available->save();
                }
            } else {
                throw new Exception(trans('drivisa::drivisa.messages.working_day_unavailable'));
            }
        } else {
            throw new Exception(trans('drivisa::drivisa.messages.working_hour_unavailable'));
        }
    }

    public function SavePaymentIntent($trainee, $payment_method_id, $totalCost)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return PaymentIntent::create([
            'customer' => $trainee->stripe_customer_id,
            'payment_method' => $payment_method_id,
            'amount' => round($totalCost * 100),
            'currency' => 'CAD',
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => "Payment made by $trainee->first_name $trainee->last_name for booking lesson"
        ]);
    }

    private function createTransfer($amount, $instructor_account)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return \Stripe\Transfer::create([
            'amount' => round($amount * 100),
            'currency' => 'CAD',
            'destination' => $instructor_account->account_id,
        ]);
    }

    public function getLessonCost($lesson_id)
    {
        $lesson = $this->workingHourRepository->find($lesson_id);
        return new WorkingHourWithCostTransformer($lesson);
    }

    public function getFinalCostForTransaction($instructor, $data): float
    {
        $cost = $this->getAdditionalPrice($instructor, $data);

        $finalCost = (float)$cost['cost'] + (float)$cost['tax'];
        $finalCost += (float)$cost['lesson_cost']['costs']['cost'] + (float)(float)$cost['lesson_cost']['costs']['tax'];

        return $finalCost;
    }

    public function getAdditionalPrice($instructor, $data, $point=null)
    {
        $distances = $this->getTotalDistance($instructor, $data ,$point);

        $cost_per_km = (int)config('settings.cost_per_km', 1);

        $totalDistance = $distances['pickDistance'] + $distances['dropDistance'];
        $total_cost = $totalDistance * $cost_per_km;

        $lesson_cost = 0;

        if (isset($data['lesson_id'])) {
            $lesson_cost = $this->getLessonCost($data['lesson_id']);
        }

        return [
            'extra_distance' => floatval($totalDistance),
            'totalDistance' => floatval($totalDistance + config('settings.free_distance', 10)),
            'free_km' => floatval((int)config('settings.free_distance', 10)),
            'cost_per_km' => $cost_per_km,
            'cost' => $total_cost,
            'tax' => floatval($total_cost * (Settings::get('lesson_tax') / 100)),
            'lesson_cost' => $lesson_cost
        ];
    }

    private function getTotalDistance($instructor, $data, $point=null): array
    {
        if (!isset($data['lesson_id'])) {
            throw new Exception("Working hour not found");
        }

        $workingHour = $this->workingHourRepository->find($data['lesson_id']);
        $instructorPoint = $point ?? $workingHour->point;
        
        if ($instructorPoint?->instructor_id !== $instructor->id) {
            throw new AuthorizationException();
        }

        $pickDistance = 0;
        $dropDistance = 0;

        $free_distance = (int)config('settings.free_distance', 10);

        $distanceCalculationService = new DistanceCalculationService;

        $pickDistance += $distanceCalculationService->setLocation(
            $instructorPoint->source_latitude,
            $instructorPoint->source_longitude,
            $data['pick_lat'],
            $data['pick_long']
        )->calculateViaGoogle();

        if (isset($data['type']) && $data['type'] == 'pick-drop') {
            $dropDistance += $distanceCalculationService->setLocation(
                $instructorPoint->source_latitude,
                $instructorPoint->source_longitude,
                $data['drop_lat'],
                $data['drop_long']
            )->calculateViaGoogle();
        }

        return [
            'pickDistance' => $free_distance < $pickDistance ? $pickDistance - $free_distance : 0,
            'dropDistance' => $free_distance < $dropDistance ? $dropDistance - $free_distance : 0
        ];
    }

    /**
     * @param $trainee
     * @param $package
     * @param $tax
     * @param $totalChargeable
     * @param $transaction
     * @return void
     */
    private function allEventVariablesThatNeedForBuyPackage($trainee, $package, $totalChargeable, $discountPrice): void
    {
        $eventVariables = [
            $trainee,
            $package,
            $totalChargeable,
            $discountPrice,
        ];

        event(new NewBuyPackage(...$eventVariables));
    }

    // package buy
    public function buyPackage($payment_method_id, $trainee, $package,$discount_id=0)
    {
        $amount = $package->packageData->discount_price;
        $discount = Discount::find((int)$discount_id);
        $discount_amount=0;
        $discount_type='-';
        if($discount) {
            $discount_id = $discount->id;
            $discount_amount = $discount->discount_amount;
            $discount_type = $discount->type;
            if ($discount->type == 'percent') {
                $discount_amount = ($discount_amount / 100) * $amount;
            }
        }
        $discountPrice = $package->packageData->discount_price - $discount_amount;
        $tax = Settings::get('lesson_tax');

        $totalChargeable = $discountPrice + ($discountPrice * ($tax / 100));

        $paymentIntent = $this->saveIntentWithoutTransfer($payment_method_id, $totalChargeable, $trainee);
        $course = $this->courseService->addCourse([
            'package_id' => $package->id,
            'user_id' => $trainee->user->id,
            'status' => Course::STATUS['initiated'],
            'type' => $package->packageType->name === "BDE" ? Course::TYPE['BDE'] : Course::TYPE['Package'],
            'credit' => $package->packageData->hours,
            'payment_intent_id' => $paymentIntent['id'],
            'transaction_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'charge_id' => $paymentIntent->charges->data[0]->id,
        ]);

        $transaction = $this->createTransactionHistory($paymentIntent);

        $this->createPurchaseHistory($transaction, $course, $trainee);

        if ($course->type === Course::TYPE['BDE']) {
            $response = $this->postGoGoService($trainee);
        }
        if($discount) {
            DiscountUser::create(
                [
                    'user_id' => $trainee->id,
                    'discount_id' => $discount->id,
                    'discount_amount' => $discount_amount,
                    'discount_type' => $discount_type,
                    'main_amount' => $amount,
                    'total_discount' => $discount_amount,
                    'after_discount' => $totalChargeable,
                    'discount_used_name' => 'buy_package',
                    'type' => 'buy_package',
                    'type_id' => $package->id,
                ]);
        }
        $this->allEventVariablesThatNeedForBuyPackage($trainee, $package, $totalChargeable, $discountPrice);
        $course['totalChargeable'] =$totalChargeable;
        $course['discountPrice'] =$discountPrice;
        $course['discount_amount'] =$discount_amount;
        $course['discount_price'] =$package->packageData->discount_price;
        $course['tax'] =$tax;
        return $course;
    }

    private function saveIntentWithoutTransfer($payment_method_id, $totalCost, $trainee)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'payment_method' => $payment_method_id,
            'amount' => round($totalCost * 100),
            'currency' => 'CAD',
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => "Payment made by $trainee->first_name $trainee->last_name for buying package"
        ]);

        return $paymentIntent;
    }

    public function createTransactionHistory($paymentIntent)
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

    private function createPurchaseHistory($transaction, $course, $trainee)
    {
        $purchase = new Purchase;

        $purchase->transaction_id = $transaction->id;
        $purchase->type = $course->type === Course::TYPE['BDE'] ? Purchase::TYPE['BDE'] : Purchase::TYPE['Package'];
        $purchase->trainee_id = $trainee->id;

        $course->purchase()->save($purchase);
    }

    /**
     * @param $trainee
     * @param $point
     * @param $transaction
     * @param $purchase
     * @param $lesson
     * @return void
     */
    private function allEventVariablesThatNeedForLessonBooking(
        $trainee,
        $point,
        $transaction = null,
        $purchase = null,
        $lesson = null,
        $is_credit = null,
        $cost = null,
        $tax = null,
        $additional_charge = null,
        $additional_tax = null,
        $extra_distance = null,
        $commission = null,
        $totalCost = null,
        $totalDiscount=0
    ): void {
        $eventVariables = [
            $trainee,
            $point->instructor,
            $transaction,
            $purchase,
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
        ];

        event(new NewLessonBooked(...$eventVariables));

        // notify instructor about booking via onesignal
        $lesson->instructor->user->notify(new InstructorBookingReceviedNotification($lesson));

        // notify trainee lesson booked via onesignal
        $lesson->trainee->user->notify(new TraineeLessonBookedNotification($lesson));
    }

    /**
     * @param $data
     * @param $point
     * @param float $additional_charge
     * @param float $additional_tax
     * @param float $extra_distance
     * @param float $total_distance
     * @return array
     */
    public function getAdditionalCostAndPoint($data, $point, float $additional_charge, float $additional_tax, float $extra_distance, float $total_distance = 0.0): array
    {
        if (isset($data['pick_drop'])) {
            if ($data['pick_drop']['type'] === 'pick') {
                $pick_point = json_encode([
                    'latitude' => $data['pick_drop']['pick_lat'],
                    'longitude' => $data['pick_drop']['pick_long'],
                    'address' => $data['pick_drop']['pick_address']
                ]);
                $drop_point = json_encode([
                    'latitude' => $point->destination_latitude,
                    'longitude' => $point->destination_longitude,
                    'address' => $point->destination_address
                ]);
            } else if ($data['pick_drop']['type'] === 'pick-drop') {
                $pick_point = json_encode([
                    'latitude' => $data['pick_drop']['pick_lat'],
                    'longitude' => $data['pick_drop']['pick_long'],
                    'address' => $data['pick_drop']['pick_address']
                ]);
                $drop_point = json_encode([
                    'latitude' => $data['pick_drop']['drop_lat'],
                    'longitude' => $data['pick_drop']['drop_long'],
                    'address' => $data['pick_drop']['drop_address']
                ]);
            } else {
                $pick_point = json_encode([
                    'latitude' => $point->source_latitude,
                    'longitude' => $point->source_longitude,
                    'address' => $point->source_address
                ]);

                $drop_point = json_encode([
                    'latitude' => $point->destination_latitude,
                    'longitude' => $point->destination_longitude,
                    'address' => $point->destination_address
                ]);
            }
        } else {
            $pick_point = json_encode([
                'latitude' => $point->source_latitude,
                'longitude' => $point->source_longitude,
                'address' => $point->source_address
            ]);

            $drop_point = json_encode([
                'latitude' => $point->destination_latitude,
                'longitude' => $point->destination_longitude,
                'address' => $point->destination_address
            ]);
        }

        if ($data['pick_drop'] && $data['pick_drop']['type'] !== 'default') {
            $locationData = $data['pick_drop'];
            $locationData['lesson_id'] = $data['lesson_id'] ?? null;
            $locationData['instructor_id'] = $point->instructor->id;


            $finalCost = $this->getAdditionalPrice($point->instructor, $locationData,$point);


            $additional_charge = (float)$finalCost['cost'];
            $additional_tax = (float)$finalCost['tax'];
            $extra_distance = (float)$finalCost['extra_distance'];
            $total_distance = (float)$finalCost['totalDistance'];
        }
        return array($data, $pick_point, $drop_point, $additional_charge, $additional_tax, $extra_distance, $total_distance);
    }

    /**
     * @param $workingDay
     * @param $workingHour
     * @param $data
     * @param $point
     * @param $trainee
     * @return void
     * @throws Exception
     */
    private function bookLesson($workingDay, $workingHour, $data, $point, $trainee, $is_credit = false): array
    {
        $date = $workingDay->date;
        $start_at = Carbon::parse($date . $workingHour->open_at);
        $end_at = Carbon::parse($date . $workingHour->close_at);
        $duration = $start_at->diffInMinutes($end_at);
        $working_hour_id = $workingHour->id;
        $additional_charge = 0;
        $additional_tax = 0;
        $extra_distance = 0;
        $paymentIntent = null;
        $totalCost=0;

        list(
            $data,
            $pick_point,
            $drop_point,
            $additional_charge,
            $additional_tax,
            $extra_distance
        ) = $this->getAdditionalCostAndPoint($data, $point, $additional_charge, $additional_tax, $extra_distance);


        if (($duration / 60) == 1) {
            $lesson_cost = round($duration * Settings::get('lesson_cost') / 60, 2);
        } else {
            $lesson_cost = round(self::LESSON_COST_2_HOUR, 2);
        }
        $lesson_tax = round($lesson_cost * Settings::get('lesson_tax') / 100, 2);
        $discount = Discount::find(@$data['discount_id']);
        $discount_main=0;
        $discount_amount=0;
        $discount_type='';
        $discountPrice=0;
        // calculate total cost
        if (!$is_credit) {

            if ($discount) {
                $discount_id = $discount?->id;
                $discount_main = $discount?->discount_amount;
                $discount_amount = $discount?->discount_amount;
                $discount_type = $discount?->type;
                if ($discount?->type == 'percent') {
                    $discount_amount = ($discount_amount / 100) * $lesson_cost;
                }
            }
            $discountPrice = $lesson_cost - $discount_amount;
            $discountPriceTax = round($discountPrice * Settings::get('lesson_tax') / 100, 2) ;
            $cost = round($lesson_cost, 2);
            $tax = round($discountPriceTax, 2);
            $totalCost = $discountPrice + $tax + $additional_charge + $additional_tax;
            $totalCost = round($totalCost, 2);
            $commission = ($totalCost * Settings::get('commission') / 100);
            $commission = round($commission, 2);

        } else {
            $cost = 0;
            $tax = 0;
            $totalCost = $cost + $tax + $additional_charge + $additional_tax;
            $totalCost = round($totalCost, 2);
            $commission = 0;
            $commission = round($commission, 2);
        }
        $instructor_account = $this->stripeAccountRepository->findByAttributes(['instructor_id' => $workingDay->instructor_id]);
        if (!$instructor_account) {
            throw new Exception(
                trans('drivisa::drivisa.messages.instructor_not_has_bank_account'),
                Response::HTTP_NOT_FOUND
            );
        }

        if (!$is_credit || $additional_charge > 0) {
            if ($data['payment_method_id'])
                $paymentIntent = $this->SavePaymentIntent($trainee, $data['payment_method_id'], $totalCost);
        }

        if ($is_credit) {
            $payment_by = Lesson::PAYMENT_BY['credit'];
        } else {
            $payment_by = Lesson::PAYMENT_BY['stripe'];
        }

        $lesson = $this->lessonRepository->create([
            'instructor_id' => $point->instructor->id,
            'lesson_type' => isset($data['bde_lesson']) && $data['bde_lesson'] == true ? Lesson::TYPE['bde'] : Lesson::TYPE['driving'],
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'no' => Carbon::now()->timestamp,
            'start_at' => $start_at,
            'start_time' => $workingHour->open_at,
            'end_at' => $end_at,
            'pickup_point' => $pick_point,
            'dropoff_point' => $drop_point,
            'cost' => $cost,
            'commission' => $commission,
            'tax' => $tax,
            'net_amount' => $totalCost - $commission,
            'transaction_id' => $paymentIntent?->charges->data[0]->balance_transaction,
            'payment_intent_id' => $paymentIntent?->id,
            'charge_id' => $paymentIntent?->charges->data[0]->id,
            'working_hour_id' => $working_hour_id,
            'payment_by' => $payment_by,
            'additional_tax' => $additional_tax,
            'additional_cost' => $additional_charge,
            'additional_km' => $extra_distance,
            'bde_number' => isset($data['bde_lesson']) && $data['bde_lesson'] == true ? $this->getBdeNumber($trainee) : null,
            'is_bonus_credit' => 0
        ]);

        $transaction = null;
        $purchase = null;
        if($discount && !$is_credit){
            DiscountUser::create(
                [
                    'user_id' => $trainee->user->id,
                    'discount_id' => $discount->id,
                    'discount_amount' => $discount_main,
                    'discount_type' => $discount_type,
                    'main_amount' => $lesson_cost,
                    'total_discount' => $discount_amount,
                    'after_discount' => $discountPrice,
                    'discount_used_name' => 'driving',
                    'type' => 'driving' ,
                    'type_id' => $lesson->id,
                ]);
        }
        if ($paymentIntent) {
            $transaction = $this->transactionService->create([
                'amount' => (float)($paymentIntent->amount / 100),
                'payment_intent_id' => $paymentIntent->id,
                'tx_id' => $paymentIntent->charges->data[0]->balance_transaction,
                'charge_id' => $paymentIntent->charges->data[0]->id,
                'response' => $paymentIntent,
            ]);

            //create purchase history

            $purchaseHistory = new Purchase;
            $purchaseHistory->transaction_id = $transaction->id;
            $purchaseHistory->type = Purchase::TYPE['lesson'];
            $purchaseHistory->trainee_id = $trainee->id;

            $purchase = $lesson->purchases()->save($purchaseHistory);
        }

        // all event variables that need for lesson booking
        $this->allEventVariablesThatNeedForLessonBooking($trainee, $point, $transaction, $purchase, $lesson, $is_credit, $cost, $tax, $additional_charge, $additional_tax, $extra_distance, $commission, $totalCost,$discount_amount);


        return [
            'lesson' => $lesson,
            'duration' => $duration
        ];
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

    private function postGoGoService($trainee): void
    {
        try {
            $client = new Client([
                'verify' => false
            ]);

            $info = [
                'firstname' => $trainee->user->first_name,
                'lastname' => $trainee->user->last_name,
                'email' => $trainee->user->email,
                'apikey' => self::GO_GO_SERVICE_API_KEY
            ];

            $client->post(
                self::GO_GO_SERVICE_URL,
                [
                    'form_params' => $info
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function buyExtraHours($data, $trainee)
    {

        if ($data['type'] !== 'BDE') {
            throw new Exception('Unable to buy extra hours for this type of course');
        }

        $paymentIntent = $this->saveIntentWithoutTransfer($data['payment_method_id'], $data['total_amount'], $trainee);

        $course = $this->courseService->addCourse([
            'user_id' => $trainee->user->id,
            'status' => Course::STATUS['initiated'],
            'type' => Course::TYPE['BDE'],
            'credit' => (int)$data['credit'],
            'payment_intent_id' => $paymentIntent['id'],
            'transaction_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'charge_id' => $paymentIntent->charges->data[0]->id,
            'is_extra_credit_hours' => 1
        ]);
        $discount = Discount::find($data['discount_id']);
        if($discount) {
            $discount_id = $discount->id;
            $discount = $discount->discount_amount;
            $discount_amount = $discount->discount_amount;
            $discount_type = $discount->type;
            if ($discount->type == 'percent') {
                $discount_amount = ($discount_amount / 100) * $data['total_amount'];
            }
            $discountPrice = $data['total_amount']-$discount_amount;
            DiscountUser::create(
                [
                    'user_id' => $trainee->id,
                    'discount_id' => $discount->id,
                    'discount_amount' => $discount,
                    'discount_type' => $discount_type,
                    'main_amount' => $data['total_amount'],
                    'total_discount' => $discount_amount,
                    'after_discount' => $discountPrice,
                    'discount_used_name' => 'buy_extra_hours',
                    'type' => 'buy_extra_hours',
                    'type_id' => (int)$data['credit'],
                ]);
        }
        $transaction = $this->createTransactionHistory($paymentIntent);

        $this->createPurchaseHistory($transaction, $course, $trainee);

        return $course;
    }

    /**
     * @param $trainee
     * @param $date
     * @param $duration
     * @param null $lessonToReschedule
     * @return void
     * @throws Exception
     */
    public function preventBookingMoreThanTwoHoursADay($trainee, $date, $duration, $lessonToReschedule = null)
    {
        $totalDuration = $trainee->lessons()
            ->whereDate('start_at', $date)
            ->whereIn('status', [Lesson::STATUS['reserved'], Lesson::STATUS['inProgress'], Lesson::STATUS['completed']])
            ->get()
            ->sum('duration');

        if ($lessonToReschedule && Carbon::parse($lessonToReschedule->start_at)->isSameDay($date))
            $totalDuration -= $lessonToReschedule->duration;

        $maxHours = $duration === 2.5 ? 2.5 : 2;
        $remainingHours = $maxHours - $totalDuration;

        if ($duration > $remainingHours) {
            throw new Exception("You cannot book lessons for more than 2 hours per day. You have already booked {$totalDuration} hours of lessons for {$date->format('D, F d, Y')}.");
        }
    }
}
