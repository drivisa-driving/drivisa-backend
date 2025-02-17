<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Car;
use Modules\Drivisa\Http\Requests\StoreCarRequest;
use Modules\Drivisa\Http\Requests\UpdateCarRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\CarService;
use Modules\Drivisa\Services\InstructorService;
use Modules\Drivisa\Transformers\CarTransformer;
use Modules\User\Repositories\UserRepository;

class CarController extends ApiBaseController
{

    public function __construct(
        public InstructorRepository $instructor,
        public CarService           $carService,
        public UserRepository       $user
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $cars = $this->carService->getCars($instructor);
            return CarTransformer::collection($cars);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getCar(Request $request, Car $car)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $car = $this->carService->getCar($instructor, $car);
            return new CarTransformer($car);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addCar(StoreCarRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->carService->addCar($instructor, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.car_added'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());

        }
    }

    public function updateCar(UpdateCarRequest $request, Car $car)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->carService->updateCar($instructor, $car, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.car_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());

        }
    }

    public function destroy(Request $request, Car $car)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->carService->deleteCar($instructor, $car);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.car_deleted'));
        } catch (\Exception $e) {
            if ($e instanceof AuthorizationException) {
                return $this->errorMessage(trans('user::auth.action_unauthorized'), Response::HTTP_FORBIDDEN);
            }
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getCarMaker(Request $request)
    {
        return CarTransformer::collection($this->carService->getCarMaker());
    }
}