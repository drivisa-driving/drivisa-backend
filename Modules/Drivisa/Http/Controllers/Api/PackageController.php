<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Services\BookingService;
use Modules\Drivisa\Services\PackageService;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Http\Requests\BuyExtraHoursRequest;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\admin\PackageTransformer;

class PackageController extends ApiBaseController
{

    /**
     * @param PackageRepository $packageRepository
     * @param PackageService $packageService
     * @param BookingService $bookingService
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        private PackageRepository $packageRepository,
        private PackageService    $packageService,
        private BookingService    $bookingService,
        private TraineeRepository $traineeRepository
    ) {
    }

    /**
     * @param $type
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Throwable
     */
    public function getPackagesByType($type)
    {
        try {
            $packages = $this->packageService->getPackageByType($type);
            return PackageTransformer::collection($packages);
        } catch (\Exception $exception) {
            return $this->errorMessage(trans("drivisa::drivisa.messages.package_type_not_found"), $exception->getCode());
        }
    }

    public function getSinglePackage(Request $request, Package $package)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return new PackageTransformer($package);
        } catch (\Exception $exception) {
            return $this->errorMessage(trans("drivisa::drivisa.messages.package_type_not_found"), $exception->getCode());
        }
    }

    public function buyPackage(Request $request, Package $package)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.login_as_trainee_error');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$trainee->verified) {
                $message = trans('drivisa::drivisa.messages.account_not_verified');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return $this->bookingService->buyPackage($request->payment_method_id, $trainee, $package,$request->discount_id);
        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }

    public function buyExtraHours(BuyExtraHoursRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.login_as_trainee_error');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$trainee->verified) {
                $message = trans('drivisa::drivisa.messages.account_not_verified');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->bookingService->buyExtraHours($request->validated(), $trainee);
        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}
