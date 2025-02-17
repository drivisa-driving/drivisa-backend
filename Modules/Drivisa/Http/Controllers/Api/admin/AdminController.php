<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\AdminCreateRequest;
use Modules\Drivisa\Http\Requests\AdminUpdateRequest;
use Modules\Drivisa\Http\Requests\UpdateAdminProfileRequest;
use Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\AdminService;
use Modules\Drivisa\Services\BookingService;
use Modules\Drivisa\Services\TraineeService;
use Modules\Drivisa\Transformers\admin\AdminProfileTransformer;
use Modules\Drivisa\Transformers\AdminTransformer;
use Modules\Drivisa\Transformers\TraineeInfoTransformer;
use Modules\Drivisa\Transformers\TraineeTransformer;
use Modules\User\Repositories\UserRepository;

class AdminController extends ApiBaseController
{
    /**
     * @var UserRepository
     */
    private $user;

    private $adminService;
    /**
     * @var AdminRepository
     */
    private $adminRepository;


    public function __construct(
        UserRepository  $user,
        AdminService    $adminService,
        AdminRepository $adminRepository
    )
    {
        $this->user = $user;
        $this->adminService = $adminService;
        $this->adminRepository = $adminRepository;
    }

    /**
     * get current login admin profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|AdminProfileTransformer
     */
    public function getProfile(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }

        $adminInfo = $this->adminService->getProfileInfo($user);
        return new AdminProfileTransformer($adminInfo);
    }

    public function blockUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $user = $this->user->find($request->user_id);

            $user->blocked_at = now();
            $user->save();
            DB::commit();

            return $this->successMessage("User Blocked");
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }


    public function unblockUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $user = $this->user->find($request->user_id);

            $user->blocked_at = null;
            $user->save();
            DB::commit();

            return $this->successMessage("User Unblocked");
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     *  update current logged in admin profile
     *
     * @param UpdateAdminProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAdminProfile(UpdateAdminProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->user->update($user, [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'address' => $request->get('address'),
                'phone_number' => $request->get('phone_number'),
                'city' => $request->get('city'),
                'postal_code' => $request->get('postal_code'),
                'province' => $request->get('province'),
            ]);
            DB::commit();
            $message = trans('drivisa::drivisa.messages.admin_profile_updated', [], $request->get('locale'));
            return $this->successMessage($message, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * get all admin list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function allAdmin(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }

        $admins = $this->adminRepository->serverPaginationFilteringFor($request);

        return AdminTransformer::collection($admins);

    }

    /**
     * create New Admin
     *
     * @param AdminCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(AdminCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->adminService->createAdmin($user, $request);

            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.admin_created'));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update existing Admin
     *
     * @param AdminUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAdmin(AdminUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->adminService->updateAdmin($user, $request);

            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.admin_updated'));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }

    public function deleteAdmin(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->adminService->delete($user, $id);

            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.admin_deleted'));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}
