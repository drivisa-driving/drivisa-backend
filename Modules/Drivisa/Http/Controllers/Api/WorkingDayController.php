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
use Modules\Drivisa\Http\Requests\UpdateWorkingDayStatusRequest;
use Modules\Drivisa\Services\WorkingDayService;
use Modules\Drivisa\Transformers\WorkingDayForTraineeTransformer;

class WorkingDayController extends ApiBaseController
{

    protected $workingDayService;

    public function __construct(WorkingDayService $workingDayService)
    {
        $this->workingDayService = $workingDayService;
    }

    /**
     * Get instructor's workingDay
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request, WorkingDay $workingDay)
    {
        try {
            return new WorkingDayForTraineeTransformer($workingDay);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update instructor's working day status
     * The request should contains working day status
     * @param UpdateWorkingDayStatusRequest $request
     * @param WorkingDay $workingDay
     * @return JsonResponse
     */
    public function updateWorkingDayStatus(UpdateWorkingDayStatusRequest $request, WorkingDay $workingDay)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->workingDayService->updateWorkingDayStatus($authUser, $workingDay, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.working_day_updated_status'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}