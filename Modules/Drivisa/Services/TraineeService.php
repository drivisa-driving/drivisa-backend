<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\BDELog;
use stdClass;
use Exception;
use Carbon\Carbon;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Illuminate\Http\Response;
use Modules\Media\Image\Imagy;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Entities\Purchase;
use Modules\Media\Services\FileService;
use Modules\Drivisa\Entities\PackageType;
use Modules\Drivisa\Services\StripeService;
use Modules\Drivisa\Entities\CreditUseHistory;
use Modules\Drivisa\Events\SendMessage;
use Modules\Media\Repositories\FileRepository;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Events\TraineeLicenceWasUploaded;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Notifications\TraineeCreditBDENotification;
use Modules\Drivisa\Notifications\SendMessageToUserNotification;
use Modules\Drivisa\Notifications\TraineeBonusCreditNotification;
use Modules\Drivisa\Notifications\TraineeCreditReduceNotification;
use Modules\Drivisa\Notifications\TraineeRefundOnLessonNotification;

class TraineeService
{
    private $trainee;
    private $fileService;
    private $imagy;
    private $file;
    private InstructorRepository $instructorRepository;
    private CourseRepository $courseRepository;
    private TransactionService $transactionService;
    private StripeService $stripeService;
    private LessonService $lessonService;

    /**
     * @param TraineeRepository $trainee
     * @param Imagy $imagy
     * @param FileRepository $file
     * @param FileService $fileService
     * @param InstructorRepository $instructorRepository
     * @param CourseRepository $courseRepository
     * @param TransactionService $transactionService
     * @param StripeService $stripeService
     * @param LessonService $lessonService
     */
    public function __construct(
        TraineeRepository    $trainee,
        Imagy                $imagy,
        FileRepository       $file,
        FileService          $fileService,
        InstructorRepository $instructorRepository,
        CourseRepository     $courseRepository,
        TransactionService   $transactionService,
        StripeService        $stripeService,
        LessonService        $lessonService
    ) {
        $this->trainee = $trainee;
        $this->fileService = $fileService;
        $this->imagy = $imagy;
        $this->file = $file;
        $this->instructorRepository = $instructorRepository;
        $this->courseRepository = $courseRepository;
        $this->transactionService = $transactionService;
        $this->stripeService = $stripeService;
        $this->lessonService = $lessonService;
    }

    public function getProfileInfo($trainee)
    {
        $info = new stdClass();
        $info->trainee = $trainee;
        $info->lessons = new stdClass();
        $info->lessons->count = $trainee->lessons()->count();
        $info->lessons->instructor = $trainee->lessons()->distinct()->count('instructor_id');
        $info->lessons->hours = $trainee->lessons->whereNotNull('ended_at')->sum(function ($item) {
            $start_at = Carbon::parse($item->end_at);
            $end_at = Carbon::parse($item->start_at);
            $duration = $start_at->diffInHours($end_at);
            return $duration;
        });
        $info->lessons->today = $trainee->lessons()->whereDate('start_at', today())->get();
        $info->lessons->upcoming = $trainee->lessons()->whereDate('start_at', '>', today())->take(10)->get();
        return $info;
    }

    public function uploadSingleDocument($user, $data)
    {
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if ($trainee === null) {
            throw new Exception(trans('drivisa::drivisa.messages.trainee_not_found'));
        }
        $file = $data['file'] ?? null;
        $zone = $data['zone'] ?? null;
        if ($file && $zone) {
            $previousFile = $trainee->filesByZone($zone)->first();
            $savedFile = $this->fileService->store($file);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                $data['zone'] => $savedFile->id
            ];
            event(new TraineeLicenceWasUploaded($trainee, $data));
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
        }
    }

    public function uploadDocuments($user, $data)
    {
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if ($trainee === null) {
            throw new Exception(trans('drivisa::drivisa.messages.trainee_not_found'));
        }
        $files = $data['files'] ?? null;
        foreach ($files as $file) {
            $previousFile = $trainee->filesByZone($file['zone'])->first();
            $savedFile = $this->fileService->store($file['file']);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                $file['zone'] => $savedFile->id
            ];
            event(new TraineeLicenceWasUploaded($trainee, $data));
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
        }
    }

    public function getAllRecentInstructors($trainee)
    {
        $instructor_ids = $trainee->lessons()->get()->pluck('instructor_id')->toArray();
        return $this->instructorRepository
            ->whereIn('id', $instructor_ids)
            ->whereHas('user', function ($query) {
                $query->whereNull('blocked_at');
            })
            ->get();
    }

    /**
     * @throws Exception
     */
    public function addCredit($trainee, $data)
    {
        $package = Package::find($data['package_id']);
        $packageType = PackageType::find($package->package_type_id);

        $paymentIntent = $this->getPaymentDetails($data['payment_intent_id']);

        if (!$paymentIntent) {
            throw new Exception('Payment Intent Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $course = $this->courseRepository->create([
            'package_id' => $data['package_id'],
            'user_id' => $trainee->user_id,
            'status' => Course::STATUS['initiated'],
            'type' => $packageType->name == "BDE" ? Course::TYPE['BDE'] : Course::TYPE['Package'],
            'credit' => (int)$data['credit'],
            'transaction_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'payment_intent_id' => $paymentIntent->id,
            'charge_id' => $paymentIntent->charges->data[0]->id
        ]);

        $course->created_at = Carbon::createFromFormat('Y-m-d', $data['payment_date']);
        $course->save();

        $transaction = $this->createTransaction($paymentIntent);
        $this->createPurchaseHistory($transaction, $trainee, $course);

        $trainee->user->notify(new TraineeCreditBDENotification($trainee, $package, $packageType));
    }

    private function getPaymentDetails($payment_intent_id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        return PaymentIntent::retrieve($payment_intent_id);
    }

    private function createTransaction($paymentIntent)
    {
        return $this->transactionService->create([
            'amount' => (float)($paymentIntent->amount / 100),
            'payment_intent_id' => $paymentIntent->id,
            'tx_id' => $paymentIntent->charges->data[0]->balance_transaction,
            'charge_id' => $paymentIntent->charges->data[0]->id,
            'response' => $paymentIntent,
        ]);
    }

    private function createPurchaseHistory($transaction, $trainee, $course)
    {
        //create purchase history

        $purchaseHistory = new Purchase;
        $purchaseHistory->transaction_id = $transaction->id;

        if ($course->type == 1) {
            $purchaseHistory->type = Purchase::TYPE['BDE'];
        } else if ($course->type == 2) {
            $purchaseHistory->type = Purchase::TYPE['Package'];
        } else if ($course->type == 7) {
            $purchaseHistory->type = Purchase::TYPE['Bonus'];
        } else if ($course->type == 8) {
            $purchaseHistory->type = Purchase::TYPE['Bonus_BDE'];
        }

        $purchaseHistory->trainee_id = $trainee->id;

        $course->purchase()->save($purchaseHistory);
    }

    public function addBonusCredit($trainee, $data)
    {
        $course = $this->courseRepository->create([
            'user_id' => $trainee->user_id,
            'status' => Course::STATUS['initiated'],
            'credit' => (int)$data['credit'],
            'type' => $data['type'] == "Bonus_BDE" ? Course::TYPE['Bonus_BDE'] : Course::TYPE['Bonus'],
        ]);

        $course->created_at = Carbon::createFromFormat('Y-m-d', $data['payment_date']);
        $course->save();

        $trainee->user->notify(new TraineeBonusCreditNotification($trainee, $data));
    }

    /**
     * @param $trainee
     * @param $data
     * @return void
     * @throws Exception
     */
    public function reduceCredit($trainee, $data)
    {
        $courses_ids = collect(DB::select("select dc.id as id, COALESCE(sum(dcuh.credit_used), 0) total_used, dc.credit as credit from drivisa__credit_use_histories dcuh
        right join drivisa__courses dc
        on dc.id = dcuh.course_id
        where dc.user_id = {$trainee->user->id}
        group by dc.id
        having total_used < credit;"))->pluck('id');

        $course_available = $trainee->user->courses()
            ->whereNotIn('status', [Course::STATUS['completed'], Course::STATUS['canceled']])
            ->whereIn('id', $courses_ids)
            ->with('package.packageData')
            ->when($data['course_type'] == "BDE", function ($query) {
                $query->whereIn('type', [Course::TYPE['BDE']]);
            })
            ->when($data['course_type'] != "BDE", function ($query) {
                $query->whereIn('type', [Course::TYPE['Package']]);
            })
            ->first();

        if (!$course_available) {
            throw new Exception(trans('drivisa::drivisa.messages.course_not_available'));
        }

        $creditUseHistory = CreditUseHistory::create([
            'course_id' => $course_available->id,
            'used_at' => now(),
            'credit_used' => round(($data['credit_reduce'])),
            'note' => $data['note']
        ]);

        $this->refundCredit($creditUseHistory->course->payment_intent_id, $creditUseHistory);

        $trainee->user->notify(new TraineeCreditReduceNotification($trainee, $course_available, $data));
    }

    /**
     * @param $lesson
     * @return void
     * @throws Exception
     */
    public function initiateRefundWhenLessonExpired($lesson)
    {
        if ($lesson->is_refund_initiated != 0)
            throw new Exception(trans('drivisa::drivisa.messages.refund_already_initiated'), Response::HTTP_NOT_FOUND);

        if ($lesson->payment_intent_id && $lesson->cost != 0.00) {
            $this->stripeService->refund($lesson->payment_intent_id);
        } else if ($lesson->payment_by === Lesson::PAYMENT_BY['credit']) {
            $paymentIntentID = $this->lessonService->getPaymentIntentFromCourse($lesson);
            $courseUseHistory = CreditUseHistory::where('lesson_id', $lesson->id)->first();

            $this->refundCredit($paymentIntentID, $courseUseHistory);

            $additional_costs = $lesson->additional_cost + $lesson->additional_tax;

            if ($lesson->payment_intent_id && $additional_costs > 0) {
                $this->stripeService->refund($lesson->payment_intent_id);
            }
        }

        $lesson->update(['is_refund_initiated' => 1]);

        $lesson->trainee->user->notify(new TraineeRefundOnLessonNotification($lesson));
    }

    /**
     * @param $paymentIntentID
     * @param $creditUseHistory
     * @return void
     */
    private function refundCredit($paymentIntentID, $creditUseHistory)
    {
        if ($paymentIntentID) {
            $totalAmount = $this->getCancellationAmountForCourse($creditUseHistory);

            if ($totalAmount > 0) {
                $this->stripeService->refundUpdated(
                    $paymentIntentID,
                    $totalAmount
                );
            }
        }
    }

    /**
     * @param $creditUseHistory
     * @return float|int|mixed
     */
    private function getCancellationAmountForCourse($creditUseHistory)
    {
        $course = $creditUseHistory->course;
        if (!$course) return 0;

        $package = $course->package;
        if (!$package) return 0;

        $transaction = $course->purchase->transaction;

        $amount = $this->lessonService->calculateHourlyPrice($transaction->amount, $package->packageData->hours);
        $amount *= $creditUseHistory->credit_used;

        return $transaction->amount - $amount;
    }

    /**
     * @return mixed
     */
    public function getUnpurchasedTrainees()
    {
        $search = request('search');
        if(request('search') != null && request('search') !=''){
            return $this->trainee->where('verified', true)->whereHas('user',function ($user) use ($search){
                    $user->where('first_name','LIKE','%'.$search.'%')
                        ->orWhere('last_name','LIKE','%'.$search.'%')
                        ->orWhere('email','LIKE','%'.$search.'%')
                        ->orWhere('phone_number','LIKE','%'.$search.'%');
            })->whereDoesntHave('purchases')
                ->latest('created_at')
                ->paginate();
        }
        return $this->trainee->where('verified', true)
            ->whereDoesntHave('purchases')
            ->latest('created_at')
            ->paginate();
    }
    public function getUnpurchasedTraineesTotal()
    {
        return $this->trainee->where('verified', true)
            ->whereDoesntHave('purchases')
            ->latest('created_at')
            ->count();
    }
    /**
     * @param $trainee
     * @param $message
     * @return void
     */
    private function allEventVariablesThatNeedForNotifyTrainee($trainee, $message)
    {
        $eventVariables = [
            $trainee,
            $message
        ];

        event(new SendMessage(...$eventVariables));
    }

    /**
     * @param $data
     * @return void
     */
    public function notifyUnpurchasedTrainees($data)
    {
        $trainees = $this->trainee->whereIn('id', $data['traineeIds'])->get();

        foreach ($trainees as $trainee) {
            if ($data['selected_option'] === "Notification") {
                $trainee->user->notify(new SendMessageToUserNotification($data['message']));
            } else if ($data['selected_option'] === "Email") {
                $this->allEventVariablesThatNeedForNotifyTrainee($trainee, $data['message']);
            }
        }
    }

    /**
     * @param $data
     * @param $trainee
     * @return void
     * @throws Exception
     */
    public function addTraineeSignature($data, $trainee)
    {
        $bdeLogs = $this->getEmptySignBdeLogs($trainee);
        if (count($bdeLogs) === 0) {
            throw new Exception('BDE Log Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        foreach ($bdeLogs as $bdeLog) {
            $bdeLog->update(['trainee_sign' => $data]);
        }
    }

    /**
     * @param $trainee
     * @return mixed
     */
    public function getEmptySignBdeLogs($trainee)
    {
        return BDELog::where('trainee_id', $trainee->id)->whereNull('trainee_sign')->get();
    }
}
