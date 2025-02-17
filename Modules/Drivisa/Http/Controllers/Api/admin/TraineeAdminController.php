<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use App\Exports\TraineeExport;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Traits\DocumentTrait;
use Modules\Drivisa\Services\LessonService;
use Modules\Drivisa\Services\TraineeService;
use Modules\Drivisa\Services\PurchaseService;
use Modules\Drivisa\Services\TransactionService;
use Modules\Drivisa\Entities\VerificationRequest;
use Modules\Drivisa\Events\TraineeAccountRejected;
use Modules\Drivisa\Events\TraineeAccountVerified;
use Modules\Drivisa\Services\RentalRequestService;
use Modules\Drivisa\Events\TraineeDocumentApproved;
use Modules\Drivisa\Events\TraineeDocumentRejected;
use Modules\Drivisa\Http\Requests\AddCreditRequest;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\TraineeAdminTableTransformer;
use Modules\Drivisa\Transformers\CourseTransformer;
use Modules\Drivisa\Transformers\RefundTransformer;
use Modules\Drivisa\Services\CompletedLessonService;
use Modules\Drivisa\Transformers\TraineeTransformer;
use Modules\Drivisa\Transformers\PurchaseTransformer;
use Modules\Drivisa\Http\Requests\ReduceCreditRequest;
use Modules\Drivisa\Transformers\LessonListTransformer;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\AddBonusCreditRequest;
use Modules\Drivisa\Transformers\LessonTraineeTransformer;
use Modules\Drivisa\Http\Requests\SendMessageToUserRequest;
use Modules\Drivisa\Transformers\CarRentalRequestTransformer;
use Modules\Drivisa\Transformers\admin\TraineeAdminTransformer;
use Modules\Drivisa\Transformers\admin\TraineeProfileAdminTransformer;
use Modules\User\Repositories\UserRepository;
use Modules\Drivisa\Notifications\SendMessageToUserNotification;

class TraineeAdminController extends ApiBaseController
{
    use DocumentTrait;

    /**
     * @param TraineeRepository $traineeRepository
     */
    private $traineeRepository;

    /**
     * @param LessonService $lessonService
     */
    private $lessonService;

    /**
     * @param TraineeService $traineeService
     */
    private $traineeService;

    /**
     * @param PurchaseService $purchaseService ;
     */
    private PurchaseService $purchaseService;

    /**
     * @param TransactionService $transactionService ;
     */
    private TransactionService $transactionService;

    /**
     * @param completedLessonService $completedLessonService ;
     */
    private CompletedLessonService $completedLessonService;

    /**
     * @param RentalRequestService $rentalRequestService ;
     */
    private RentalRequestService $rentalRequestService;

    /**
     * @param UserRepository $user
     */
    private UserRepository $user;

    public function __construct(
        TraineeRepository      $traineeRepository,
        TraineeService         $traineeService,
        LessonService          $lessonService,
        PurchaseService        $purchaseService,
        TransactionService     $transactionService,
        CompletedLessonService $completedLessonService,
        RentalRequestService   $rentalRequestService,
        UserRepository         $user
    )
    {
        $this->traineeRepository = $traineeRepository;
        $this->traineeService = $traineeService;
        $this->lessonService = $lessonService;
        $this->purchaseService = $purchaseService;
        $this->transactionService = $transactionService;
        $this->completedLessonService = $completedLessonService;
        $this->rentalRequestService = $rentalRequestService;
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        try {
            if (!$request->page) {
                return TraineeAdminTableTransformer::collection($this->traineeRepository->all());
            }
            if ($request->type !=='null') {
                if($search !='null' && $search !=''){
                    return \response()->json(['data' => TraineeAdminTableTransformer::collection(
                        Trainee::with('user')->where('first_name','LIKE','%'.$search.'%')
                        ->orWhere('last_name','LIKE','%'.$search.'%')
                        ->orWhereHas('user',function ($user) use ($search){
                            $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                        })->where('verified', $request->type)->paginate()),

                        'total' => Trainee::with('user')->where('first_name','LIKE','%'.$search.'%')
                            ->orWhere('last_name','LIKE','%'.$search.'%')
                            ->orWhereHas('user',function ($user) use ($search){
                                $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                            })->where('verified', $request->type)->count(),'type'=>1]);
                }
                return \response()->json(['data' => TraineeAdminTableTransformer::collection($this->traineeRepository->where('verified', $request->type)->paginate()),
                    'total' => $this->traineeRepository->where('verified', $request->type)->count(),'type'=>2]);
            }else{
                if($search !='null' && $search !=''){
                    return \response()->json(['data' => TraineeAdminTableTransformer::collection(
                        Trainee::with('user')->where('first_name','LIKE','%'.$search.'%')
                        ->orWhere('last_name','LIKE','%'.$search.'%')
                        ->orWhereHas('user',function ($user) use ($search){
                            $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                        })
                        ->paginate()),
                        'total' => Trainee::with('user')->where('first_name','LIKE','%'.$search.'%')
                            ->orWhere('last_name','LIKE','%'.$search.'%')
                            ->orWhere('last_name','LIKE','%'.$search.'%')
                            ->orWhereHas('user',function ($user) use ($search){
                                $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                            })->count(),'type'=>1]);
                }
                return \response()->json(['data' => TraineeAdminTableTransformer::collection($this->traineeRepository->paginate()), 'total' => $this->traineeRepository->count(),'type'=>3]);
            }
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
      public function export(Request $request){
          return Excel::download(new TraineeExport($request,$this->traineeRepository), 'trainee.xlsx');
    }

    public function verifyOrRejectTrainee(Request $request, Trainee $trainee)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee->verificationRequest()->updateOrCreate([
                'status' => $request->verified
                    ? VerificationRequest::STATUS['verified']
                    : VerificationRequest::STATUS['unverified'],
                'verified_by' => $user->id,
                'verified_at' => $request->verified
                    ? Carbon::now()->format('Y-m-d H:i:s')
                    : null,
                'message' => $request->message
            ]);


            $this->updateAllDocumentStatus([
                'entity_id' => $trainee->id,
                'status' => $request->verified ? 'approved' : 'reject',
                'reason' => $request->message
            ], 'trainee');

            $trainee->update([
                'verified' => $request->verified ? 1 : 0,
                'kyc_verification' => $request->verified ? Trainee::KYC['Approved'] : Trainee::KYC['Rejected']
            ]);
            DB::commit();
            if ($request->verified) {
                // event(new TraineeAccountVerified($trainee, $request->message));
                $trainee->update(['verified_at' => now()]);
                return $this->successMessage(trans('drivisa::drivisa.messages.trainee_verified'));
            } else {
                // event(new TraineeAccountRejected($trainee, $request->message));
                return $this->successMessage(trans('drivisa::drivisa.messages.trainee_rejected'));
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function show(Trainee $trainee)
    {
        try {
            return new TraineeProfileAdminTransformer($trainee);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function details($id)
    {
        try {
            return new TraineeAdminTransformer(Trainee::find($id));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function changeDocumentStatus(Request $request, Trainee $trainee, $document_id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            DB::commit();

            $data = [
                'id' => $document_id,
                'status' => $request->status,
                'reason' => $request->message
            ];

            $message = $request->message;

            if ($request->status == 'approved') {
                $this->updateStatus($data);
                $message = "Your Account is Activated";
                // Notify trainee about document verified
                $trainee->user->notify(new SendMessageToUserNotification($message));
                event(new TraineeDocumentApproved());
                return $this->successMessage("Document Verified");
            } else {
                $this->updateStatus($data);
                // Notify trainee about document rejection with reason
                $trainee->user->notify(new SendMessageToUserNotification($message));
                event(new TraineeDocumentRejected());
                return $this->successMessage("Document Rejected");
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    private function updateStatus($data)
    {
        DB::table('media__imageables')
            ->where('file_id', $data['id'])
            ->update([
                'status' => $data['status'] == 'approved' ? 2 : 3,
                'reason' => $data['reason']
            ]);
    }

    public function all(Request $request)
    {
        try {
            return $this->traineeRepository->where("verified", 1)->get();
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addCredit(AddCreditRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->traineeRepository->find($request->trainee_id);

            if (!$trainee) {
                $message = trans('user::users.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->traineeService->addCredit($trainee, $request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineePurchases(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $purchases = $this->purchaseService->getHistory($trainee);
            return PurchaseTransformer::collection($purchases);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeCourses(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return new CourseTransformer($trainee->user);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeRefunds(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $refunds = $this->transactionService->getTraineeRefunds($trainee, $request);
            return response()->json(['data' => RefundTransformer::collection($refunds)]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeLessonsList(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->lessonService->getTraineeLessonList($trainee);
            return new LessonListTransformer($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeLessonsHistory(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return LessonTraineeTransformer::collection($this->lessonService->getTraineePastLessonList($trainee, $request->all()));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeCompletedLessons(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->completedLessonService->traineeCompletedLessons($trainee);
            return LessonTraineeTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function reduceCredit(ReduceCreditRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->traineeRepository->find($request->trainee_id);
            if (!$trainee) {
                $message = trans('user::users.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->traineeService->reduceCredit($trainee, $request->all());
            DB::commit();

            return $this->successMessage(trans('drivisa::drivisa.messages.credit_reduced_successfully'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addBonusCredit(AddBonusCreditRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->traineeRepository->find($request->trainee_id);
            if (!$trainee) {
                $message = trans('user::users.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->traineeService->addBonusCredit($trainee, $request->all());
            DB::commit();

            return $this->successMessage(trans('drivisa::drivisa.messages.bonus_credit_added'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function initiateRefundWhenLessonExpired(Request $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->traineeService->initiateRefundWhenLessonExpired($lesson);
            DB::commit();

            return $this->successMessage(trans('drivisa::drivisa.messages.refund_initiated_successfully'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getAllRentalRequestsForTrainee(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$trainee) {
                $message = trans('user::users.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getRequests($trainee, $request->all());
            return CarRentalRequestTransformer::collection($rentalRequests);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getUnpurchasedTrainees(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return \response(['data'=>TraineeTransformer::collection($this->traineeService->getUnpurchasedTrainees()), 'total'=>$this->traineeService->getUnpurchasedTraineesTotal()]) ;
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function notifyUnpurchasedTrainees(SendMessageToUserRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->traineeService->notifyUnpurchasedTrainees($request->validated());
            return $this->successMessage(trans('drivisa::drivisa.messages.notified_successfully'));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addTraineeSignature(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $user = $this->user->findByAttributes(['username' => $request->username]);
            if (!$user) {
                $message = trans('drivisa::drivisa.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = Trainee::where('user_id', $user->id)->first();
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->traineeService->addTraineeSignature($request->signatureData, $trainee);
            return $this->successMessage(trans('drivisa::drivisa.messages.sign_added_successfully'));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getEmptySignBdeLogs(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $user = $this->user->findByAttributes(['username' => $request->username]);
            if (!$user) {
                $message = trans('drivisa::drivisa.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = Trainee::where('user_id', $user->id)->first();
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->traineeService->getEmptySignBdeLogs($trainee);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
