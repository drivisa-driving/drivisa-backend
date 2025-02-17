<?php

namespace Modules\User\Http\Controllers\Api;

use Modules\User\Transformers\UserProfileTransformer;
use Modules\User\Transformers\FullUserTransformer;
use Modules\User\Repositories\UserRepository;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Requests\UpdateUserProfilePictureRequest;
use Modules\User\Http\Requests\DeleteUserProfilePictureRequest;
use Modules\User\Http\Requests\CreateUserRequest;
use Modules\User\Http\Requests\ChangePasswordRequest;
use Modules\User\Events\UserProfilePictureWasUpdated;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Events\ChangeUserPassword;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Contracts\Authentication;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Image\Imagy;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class UserController extends ApiBaseController
{
    /**
     * @var UserRepository
     */
    private $user;
    /**
     * @var PermissionManager
     */
    private $permissions;
    /**
     * @var FileService
     */
    private $fileService;
    private $imagy;
    private $file;

    public function __construct(
        UserRepository    $user,
        PermissionManager $permissions,
        FileRepository    $file,
        Imagy             $imagy,
        FileService       $fileService
    ) {
        $this->user = $user;
        $this->permissions = $permissions;
        $this->fileService = $fileService;
        $this->imagy = $imagy;
        $this->file = $file;
    }

    public function index(Request $request)
    {
        $request['company_id'] = $this->getUserFromRequest($request)->company_id;
        return FullUserTransformer::collection($this->user->serverPaginationFilteringFor($request));
    }

    public function find(User $user)
    {
        return new FullUserTransformer($user->load('roles'));
    }


    public function store(CreateUserRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);
        $data['company_id'] = $this->getUserFromRequest($request)->company_id;
        $user = $this->user->createWithRoles($data, $request->get('roles'), $request->get('activated'));

        return response()->json([
            'user' => new FullUserTransformer($user->load('roles')),
            'message' => trans('user::users.messages.user_created'),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function mergeRequestWithPermissions(Request $request): array
    {
        $permissions = $this->permissions->clean($request->get('permissions'));

        return array_merge($request->all(), ['permissions' => $permissions]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $authUser = $this->getUserFromRequest($request);

        if ($authUser->company_id != $user->company_id) {
            return response()->json([
                'message' => trans('user::users.messages.user_not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $this->mergeRequestWithPermissions($request);

        $this->user->updateAndSyncRoles($user->id, $data, $request->get('roles'));

        return response()->json([
            'user' => new FullUserTransformer($user),
            'message' => trans('user::messages.user updated'),
        ], Response::HTTP_OK);
    }

    public function destroy(User $user, Request $request)
    {
        $this->user->delete($user->id);

        return response()->json([
            'message' => trans('user::users.messages.user_deleted'),
        ], Response::HTTP_OK);
    }

    public function sendResetPassword(User $user, Authentication $auth)
    {
        $code = $auth->createReminderCode($user);

        event(new UserHasBegunResetProcess($user, $code));

        return response()->json([
            'message' => trans('user::auth.reset_password_email_sent'),
        ], Response::HTTP_OK);
    }

    public function changePassword(ChangePasswordRequest $request)
    {

        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            if (!Hash::check($request->get('current_password'), $user->password)) {
                $message = trans('user::users.validation.password_incorrect', ['field' => trans('user::users.form.current_password')]);
                return $this->errorMessage($message, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $this->user->update($user, ['password' => $request->get('new_password')]);
            DB::commit();
            $message = trans('user::users.messages.user_password_updated');
            return $this->successMessage($message);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update user's profile
     * The request should contains new picture
     * @param UpdateUserProfilePictureRequest $request
     * @return JsonResponse|UserProfileTransformer
     */
    public function updateUserProfilePicture(UpdateUserProfilePictureRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $zone = $request->get('zone');
            $previousFile = $user->filesByZone($zone)->first();
            $savedFile = $this->fileService->store($request->file('picture'));
            if (is_string($savedFile)) {
                return $this->errorMessage($savedFile, 409);
            }
            $data['medias_single'] = [
                $zone => $savedFile->id
            ];
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
            event(new UserProfilePictureWasUpdated($user, $data));
            DB::commit();
            return new UserProfileTransformer($user);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * delete user's profile
     * @return JsonResponse|UserProfileTransformer
     */
    public function deleteUserProfilePicture(DeleteUserProfilePictureRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $zone = $request->get('zone');
            $previousFile = $user->filesByZone($zone)->first();
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
            DB::commit();
            return new UserProfileTransformer($user);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * delete user's account
     * @return JsonResponse|UserProfileTransformer
     */
    public function deleteAccount(Request $request, $username)
    {
        $user = User::whereUsername($username)->firstOrFail();

        $user->delete = 1;
        $user->save();

        return response()->json([
            'message' => trans('user::users.messages.user_deleted'),
        ], Response::HTTP_OK);
    }

    public function changeUserPassword(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
            $password = substr($random, 0, 10);

            $this->user->update($user, ['password' => $password]);
            DB::commit();

            event(new ChangeUserPassword($user, $password));

            $message = trans('user::users.messages.user_password_updated');
            return $this->successMessage($message);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
