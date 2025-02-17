<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Http\Requests\CopyScheduleRequest;
use Modules\Drivisa\Http\Requests\StoreScheduleRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\InstructorService;
use Modules\Drivisa\Services\PaymentTransferService;
use Modules\Drivisa\Services\ScheduleService;
use Modules\Drivisa\Transformers\ScheduleTransformer;
use Modules\Drivisa\Transformers\WorkingDayForTraineeTransformer;
use Modules\Drivisa\Transformers\WorkingDayTransformer;
use Modules\User\Notifications\InstructorPromotionMoneyTransferNotification;

class ScheduleController extends ApiBaseController
{
    /**
     * @var ScheduleService
     */
    protected $scheduleService;
    protected $instructorRepository;
    protected $instructorService;
    protected PaymentTransferService $paymentTransferService;

    const INSTRUCTOR_PROMOTIONS_HOURS = 40;
    const INSTRUCTOR_PROMOTIONS_AMOUNT = 100;

    public function __construct(
        ScheduleService        $scheduleService,
        InstructorRepository   $instructorRepository,
        InstructorService      $instructorService,
        PaymentTransferService $paymentTransferService
    ) {
        $this->scheduleService = $scheduleService;
        $this->instructorRepository = $instructorRepository;
        $this->instructorService = $instructorService;
        $this->paymentTransferService = $paymentTransferService;
    }

    /**
     * Get instructor's schedule
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($instructor === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.instructor_not_found'));
            }
            $workingDays = $this->scheduleService->getSchedule($instructor, $request);
            return ScheduleTransformer::collection($workingDays);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Save instructor's schedule
     * The request should contains working day date,status,copy,exclude,from,to
     * Working hours is array contains status,open_at,close_at,point_id
     * @param StoreScheduleRequest $request
     * @return WorkingDayTransformer
     */
    public function saveSchedule(StoreScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $workingDay = $this->scheduleService->saveSchedule($authUser, $request->all());

            // check if user eligible for promotion credit
            $this->checkIfUserEligibleForPromotionCredit($authUser);

            DB::commit();
            return new  WorkingDayTransformer($workingDay);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * copy instructor's schedule
     * The request should contains working day id,exclude,from,to
     * @param CopyScheduleRequest $request
     * @return JsonResponse
     */
    public function copySchedule(CopyScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->scheduleService->copySchedule($authUser, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.schedule_copied'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Get specific  instructor's schedule
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getInstructorSchedule(Request $request, $username)
    {
        try {
            $instructor = $this->instructorService->findByUsername($username);
            $request['working_day_status'] = WorkingDay::STATUS['available'];
            $workingDays = $this->scheduleService->getSchedule($instructor, $request);
            return WorkingDayForTraineeTransformer::collection($workingDays);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $code = $e->getCode());
        }
    }

    private function getTotalHours($instructor_id)
    {
        $query = "SELECT 
                    SUM(HOUR(TIMEDIFF(wh.close_at, wh.open_at))) duration
                    FROM drivisa__working_hours wh 
                    left JOIN drivisa__working_days wd
                    ON wd.id = wh.working_day_id
                    WHERE wd.instructor_id = " . $instructor_id;

        $result = DB::select($query);

        return count($result) > 0 ? $result[0]->duration : 0;
    }

    private function sendPromotionMoney($instructor): bool
    {
        $instructor_account = $instructor->stripeAccount;

        $isMoneySent = false;
        if ($instructor_account) {
            $this->paymentTransferService->createTransfer(self::INSTRUCTOR_PROMOTIONS_AMOUNT, $instructor_account);
            $isMoneySent = true;
        }

        return $isMoneySent;
    }

    /**
     * @param $authUser
     * @return void
     */
    private function checkIfUserEligibleForPromotionCredit($authUser): void
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $authUser->id]);

        if (
            $this->getTotalHours($instructor->id) >= self::INSTRUCTOR_PROMOTIONS_HOURS &&
            $instructor->promotion_level == 0
        ) {
            $instructor->update(['promotion_level' => 1]);
            //          Stop sending money on the request of Basel by Deep
            //            if ($this->sendPromotionMoney($instructor)) {
            //                $instructor->user->notify(new InstructorPromotionMoneyTransferNotification(self::INSTRUCTOR_PROMOTIONS_AMOUNT));
            //            }
        }
    }
}
