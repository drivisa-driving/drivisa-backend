<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Http\Requests\UpdateWorkingHourRequest;
use Modules\Drivisa\Http\Requests\UpdateWorkingHourStatusRequest;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\ScheduleService;
use Modules\Drivisa\Services\WorkingHourService;
use Modules\Drivisa\Transformers\WorkingHourTransformer;
use Modules\Drivisa\Transformers\WorkingHourWithCostTransformer;

class WorkingHourController extends ApiBaseController
{
    /**
     * @var ScheduleService
     */
    protected $workingHourService;
    private $trainee;

    public function __construct(
        WorkingHourService $workingHourService,
        TraineeRepository  $trainee
    ) {
        $this->workingHourService = $workingHourService;
        $this->trainee = $trainee;
    }

    /**
     * update instructor's workingHour
     * The request should  contains status,open_at,close_at,point_id
     * @param UpdateWorkingHourRequest $request
     * @return WorkingHourTransformer
     */
    public function updateWorkingHour(UpdateWorkingHourRequest $request, WorkingHour $workingHour)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $workingHour = $this->workingHourService->updateWorkingHour($workingHour, $authUser, $request->all());
            DB::commit();
            return new WorkingHourTransformer($workingHour);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update instructor's working hour status
     * The request should contains working hour status
     * @param UpdateWorkingHourStatusRequest $request
     * @param WorkingHour $workingHour
     * @return JsonResponse
     */
    public function updateWorkingHourStatus(UpdateWorkingHourStatusRequest $request, WorkingHour $workingHour)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->workingHourService->updateWorkingHourStatus($authUser, $workingHour, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.working_hour_updated_status'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function deleteWorkingHour(Request $request, WorkingHour $workingHour)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->workingHourService->deleteWorkingHour($workingHour, $authUser);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.working_hour_deleted'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getWorkingHour(Request $request, WorkingHour $workingHour)
    {
        try {
            $user = $this->getUserFromRequest($request);
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $workingHour->trainee = $trainee; // Add additional data
            return new WorkingHourWithCostTransformer($workingHour);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function makeWorkingHourAvailable(Request $request, WorkingHour $workingHour)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }

            $this->workingHourService->makeWorkingHourAvailable($workingHour, $user);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.schedule_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
