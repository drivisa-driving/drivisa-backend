<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\PackageStoreRequest;
use Modules\Drivisa\Http\Requests\PackageTypeStoreRequest;
use Modules\Drivisa\Http\Requests\PackageTypeUpdateRequest;
use Modules\Drivisa\Http\Requests\PackageUpdateRequest;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PackageTypeRepository;
use Modules\Drivisa\Services\PackageService;
use Modules\Drivisa\Services\PackageTypeService;
use Modules\Drivisa\Transformers\admin\PackageTransformer;
use Modules\Drivisa\Transformers\admin\PackageTypeTransformer;

class PackageController extends ApiBaseController
{
    /**
     * @var PackageRepository
     */
    private $packageRepository;
    /**
     * @var PackageService
     */
    private $packageService;


    /**
     * @param PackageRepository $packageRepository
     * @param PackageService $packageService
     */
    public function __construct(
        PackageRepository $packageRepository,
        PackageService    $packageService
    ) {

        $this->packageRepository = $packageRepository;
        $this->packageService = $packageService;
    }

    public function index(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageTypes = $this->packageRepository->serverPaginationFilteringFor($request);
            return PackageTransformer::collection($packageTypes);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }    public function allPackages(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageTypes = $this->packageRepository->all();
            return PackageTransformer::collection($packageTypes);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function all(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packages = $this->packageRepository->all();
            return PackageTransformer::collection($packages);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function onlyBde(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packages = $this->packageService->getPackageByType('BDE');
            return PackageTransformer::collection($packages);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function store(PackageStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->packageService->save($request);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_created', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function single(Request $request, $id)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageType = $this->packageService->findOne($id);
            if ($packageType) {
                return new PackageTransformer($packageType);
            } else {
                $message = trans('drivisa::drivisa.messages.package_not_found', [], $request->get('locale'));
                return $this->errorMessage($message);
            }
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function update(PackageUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->packageService->update($request, $id);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_updated', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function delete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->packageService->delete($id);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_deleted', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getSelectedPackages(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packages = $this->packageRepository->whereIn('package_type_id', [21, 22])->get();
            return PackageTransformer::collection($packages);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
