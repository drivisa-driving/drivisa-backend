<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Car;
use Modules\Drivisa\Entities\SavedLocation;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Http\Requests\SavedLocationCreateRequest;
use Modules\Drivisa\Http\Requests\StoreCarRequest;
use Modules\Drivisa\Http\Requests\UpdateCarRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\SavedLocationRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\CarService;
use Modules\Drivisa\Services\InstructorService;
use Modules\Drivisa\Services\SavedLocationService;
use Modules\Drivisa\Transformers\CarTransformer;
use Modules\Drivisa\Transformers\SavedLocationTransformer;
use Modules\User\Repositories\UserRepository;

class SavedLocationController extends ApiBaseController
{
    private SavedLocationRepository $savedLocationRepository;
    private SavedLocationService $savedLocationService;
    private TraineeRepository $traineeRepository;

    /**
     * @param SavedLocationRepository $savedLocationRepository
     * @param SavedLocationService $savedLocationService
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        SavedLocationRepository $savedLocationRepository,
        SavedLocationService    $savedLocationService,
        TraineeRepository       $traineeRepository
    )
    {

        $this->savedLocationRepository = $savedLocationRepository;
        $this->savedLocationService = $savedLocationService;
        $this->traineeRepository = $traineeRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLocation(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return SavedLocationTransformer::collection($trainee->savedLocations);

        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param SavedLocationCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLocation(SavedLocationCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->savedLocationService->create($trainee, $request->all());

            DB::commit();

            return $this->successMessage("Location Saved");

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param SavedLocation $savedLocation
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLocation(Request $request, SavedLocation $savedLocation)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->savedLocationService->delete($trainee, $savedLocation);

            DB::commit();

            return $this->successMessage("Location Deleted", 200);

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function combined(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->savedLocationService->saveCombinedLocation($trainee, $request->all());

            DB::commit();

            return $this->successMessage("Location Added", 200);

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}