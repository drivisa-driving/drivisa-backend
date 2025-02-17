<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\PackageStoreRequest;
use Modules\Drivisa\Http\Requests\PackageTypeStoreRequest;
use Modules\Drivisa\Http\Requests\PackageTypeUpdateRequest;
use Modules\Drivisa\Repositories\PackageTypeRepository;
use Modules\Drivisa\Services\PackageTypeService;
use Modules\Drivisa\Transformers\admin\PackageTypeTransformer;
use Modules\Drivisa\Transformers\admin\TraineeAdminTransformer;

class PackageTypeController extends ApiBaseController
{
    /**
     * @var PackageTypeRepository
     */
    private $packageTypeRepository;
    /**
     * @var PackageTypeService
     */
    private $packageTypeService;

    /**
     * @param PackageTypeRepository $packageTypeRepository
     * @param PackageTypeService $packageTypeService
     */
    public function __construct(
        PackageTypeRepository $packageTypeRepository,
        PackageTypeService    $packageTypeService
    ) {
        $this->packageTypeRepository = $packageTypeRepository;
        $this->packageTypeService = $packageTypeService;
    }


    public function allPackages(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageTypes = $this->packageTypeService->allPackageType();
            return PackageTypeTransformer::collection($packageTypes);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function index(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageTypes = $this->packageTypeRepository->serverPaginationFilteringFor($request);
            return PackageTypeTransformer::collection($packageTypes);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function store(PackageTypeStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->packageTypeService->save($request);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_type_created', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
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

            $packageType = $this->packageTypeService->findOne($id);
            if ($packageType) {
                return new PackageTypeTransformer($packageType);
            } else {
                $message = trans('drivisa::drivisa.messages.package_type_not_found', [], $request->get('locale'));
                return $this->errorMessage($message);
            }
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function update(PackageTypeUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->packageTypeService->update($request, $id);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_type_updated', [], $request->get('locale'));
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

            $this->packageTypeService->delete($id);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.package_type_deleted', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function withPackagesCount(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $packageTypes = $this->packageTypeService->withPackagesCount();
            return PackageTypeTransformer::collection($packageTypes);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
