<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Drivisa\Entities\TrainingLocation;
use Modules\Drivisa\Services\TrainingLocationService;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\TrainingLocationRepository;
use Modules\Drivisa\Transformers\admin\TrainingLocationTransformer;
use Modules\Drivisa\Http\Requests\StoreTrainingLocationRequest;
use Modules\Drivisa\Http\Requests\UpdateTrainingLocationRequest;
use Illuminate\Http\Response;

class TrainingLocationController extends ApiBaseController
{

    public function __construct(
        public TrainingLocationService    $trainingLocationService,
        public TrainingLocationRepository $trainingLocationRepository
    ) {
    }

    public function allTrainingLocations(Request $request)
    {
        try {
            $trainingLocations = $this->trainingLocationRepository->serverPaginationFilteringFor($request);
            return TrainingLocationTransformer::collection($trainingLocations);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addLocation(StoreTrainingLocationRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->trainingLocationService->addLocation($request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.location_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function updateLocation(UpdateTrainingLocationRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainingLocation = TrainingLocation::find($request->id);
            $this->trainingLocationService->updateLocation($trainingLocation, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.location_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function deleteLocation(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->trainingLocationService->deleteLocation($request->id);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.location_deleted'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}
