<?php

namespace Modules\User\Http\Controllers\Api;


use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\UserDevice;
use Modules\User\Services\UserResetter;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Events\AccountActivated;
use Modules\User\Contracts\Authentication;
use Modules\User\Services\UserRegistration;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\ResetRequest;
use Modules\User\Repositories\UserRepository;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Transformers\TokenTransformer;
use Modules\User\Services\ReferralCodeGenerator;
use Modules\User\Http\Requests\ActivationRequest;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Repositories\UserTokenRepository;
use Modules\User\Http\Requests\ResetCompleteRequest;
use Modules\User\Transformers\PublicUserTransformer;
use Modules\User\Repositories\ReferralCodeRepository;
use Modules\User\Transformers\LoginHistoryTransformer;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Modules\User\Http\Requests\ResendActivationCodeRequest;

class AuthController extends ApiBaseController
{
    /**
     * @var Authentication
     */
    private $auth;
    /**
     * @var UserRepository
     */
    private $user;
    private $userTokenRepository;

    public function __construct(
        Authentication                $auth,
        UserRepository                $user,
        UserTokenRepository           $userTokenRepository,
        public ReferralCodeRepository $referralCodeRepository
    )
    {
        $this->auth = $auth;
        $this->user = $user;
        $this->userTokenRepository = $userTokenRepository;
    }

    /**
     * Login user
     * The request should contains the Email and Password
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function postLogin(LoginRequest $request)
    {
        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            $remember = (bool)$request->get('remember_me', false);
            if ($this->auth->login($credentials, $remember)) {
                $user = $this->auth->user();

                if ($user->delete === 1) {
                    return response()->json([
                        'message' => "Your Account has been deleted"
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $this->setPlayerId($request, $user);
                Sentinel::logout($user, true);
                return response()->json([
                    'user' => new PublicUserTransformer($user),
                    'message' => trans('user::auth.successfully_logged_in')
                ], Response::HTTP_OK);
            } else {
                $message = trans('user::auth.invalid_login_password');
                return response()->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            if ($e instanceof NotActivatedException) {
                return response()->json([
                    'username' => $e->getUser()->username,
                    'email' => $e->getUser()->email,
                    'message' => trans('user::auth.account_not_yet_activated')
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            } elseif ($e instanceof ThrottlingException) {
                return response()->json([
                    'message' => trans('user::auth.account_is_blocked', ['delay' => $e->getDelay()])
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addDeviceToken(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            if (UserDevice::where('player_id', $request->player_id)->where('user_id', $user->id)->exists()) {
                return response()->json(['message' => 'Player Id already exists'], 200);
            }
            $user->userDevices()->create($request->only('player_id'));
            return response()->json(['message' => 'Player Id Created'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register new user
     * The request should contains the Firstname,Lastname,Email and Password
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function postRegister(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {

            if (!$this->checkReferCode($request->refer_code)) {
                return $this->errorMessage('Refer Code is not valid', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = app(UserRegistration::class)->register($request->all());

            if ($request->refer_code) {
                $referer = $this->referralCodeRepository
                    ->where('code', $request->refer_code)
                    ->whereNull('used_at')
                    ->first();

                $this->useReferralCode($referer, $user);

                $this->updateIfSendItNull($referer);

                if (!$this->checkIfCodeExists($request)) {
                    $this->createNewReferralCode($referer);
                }

                $user->refer_id = $referer->user_id;
                $user->save();
            }

            $this->setPlayerId($request, $user);

            DB::commit();
            return response()->json([
                'user' => new PublicUserTransformer($user),
                'message' => trans('user::auth.account_created_check_email_activation')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Activate user account
     * The request should contains the UserName and Activation code
     * @param ActivationRequest $request
     * @return JsonResponse
     */
    public function completeActivation(ActivationRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->user->findByAttributes(['username' => $request->get('username')]);
            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'message' => trans('user::users.messages.user_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }

            if ($user->isActivated()) {
                return response()->json([
                    'message' => trans('user::auth.account_already_activated_you_can_login'),
                    'username' => $user->username,
                    'email' => $user->email,
                ], Response::HTTP_OK);
            }

            if ($this->auth->activate($user->id, $request->get('code'))) {
                if (!$user->getFirstApiKey()) {
                    app(UserTokenRepository::class)->generateFor($user->id);
                }

                DB::commit();

                event(new AccountActivated($user));

                return response()->json([
                    'message' => trans('user::auth.account_activated_you_can_login'),
                    'username' => $user->username,
                    'email' => $user->email,
                ], Response::HTTP_OK);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => trans('core::core.messages.something_wrong'),
                    'username' => $user->username,
                    'email' => $user->email,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * When the user needs to reset the password
     * The request should contains the email address that needs to receive the activation link
     * @param ResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetRequest $request)
    {
        DB::beginTransaction();
        try {
            app(UserResetter::class)->startReset($request->all());

            DB::commit();
            return response()->json([
                'message' => trans('user::auth.reset_password_email_sent'),
                'username' => User::where('email', $request->email)->first()->username,
                'email' => $request->email,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof UserNotFoundException) {
                return response()->json([
                    'message' => trans('user::users.messages.user_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * When the user click on the activation link that was sent to his/her email
     * The request should contains the User ID, Activation code, New password confirmed and the language variable (locale)
     * @param ResetCompleteRequest $request
     * @return JsonResponse
     */
    public function completeResetPassword(ResetCompleteRequest $request)
    {
        DB::beginTransaction();
        try {
            app(UserResetter::class)->finishReset($request->all());
            DB::commit();
            $user = User::where('username', $request->username)->first();

            return response()->json([
                'message' => trans('user::auth.reset_password_successfully'),
                'username' => $user->username,
                'email' => $user->email,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof UserNotFoundException) {
                return response()->json([
                    'message' => trans('user::users.messages.user_not_found')
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Resend activation code to the user's email address to receive the activation code
     * The request should contains the UserName or Email
     * @param ResendActivationCodeRequest $request
     * @return JsonResponse
     */
    public function resendActivationCode(ResendActivationCodeRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->has('username')) {
                $user = $this->user->findByAttributes(['username' => $request->get('username')]);
            } else {
                $user = $this->user->findByAttributes(['email' => $request->get('email')]);
            }
            if (!$user) {
                return response()->json([
                    'message' => trans('user::users.messages.user_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }
            $isSend = $this->auth->resendActivationCode($user);
            if (!$isSend) {
                DB::rollBack();
                return response()->json([
                    'message' => trans('user::auth.account_already_activated_you_can_login'),
                    'username' => $user->username,
                    'email' => $user->email,
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            DB::commit();
            return response()->json([
                'message' => trans('user::auth.verification_email_has_been_resend'),
                'username' => $user->username,
                'email' => $user->email,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refresh token
     * The request should contains old token
     */
    public function refreshToken(Request $request)
    {
        DB::beginTransaction();
        try {
            $token = $this->userTokenRepository->findByAttributes(['access_token' => $request->get('token')]);
            if ($token) {
                $this->userTokenRepository->refreshFor($token);
                DB::commit();
                return new TokenTransformer($token);
            } else {
                DB::rollBack();
                return response()->json(['message' => trans('user::users.messages.token_not_valid')], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * logout
     */
    public function logout(Request $request)
    {
        DB::beginTransaction();
        try {
            $accessToken = str_replace('Bearer ', '', $request->header('Authorization'));
            $token = $this->userTokenRepository->findByAttributes(['access_token' => $accessToken]);
            if ($token) {
                $this->removePlayerId($request);
                $this->userTokenRepository->destroy($token);
                DB::commit();
                return response()->json(['message' => trans('user::users.messages.logout_successfully')]);
            } else {
                DB::rollBack();
                return response()->json(['message' => trans('user::users.messages.token_not_valid')], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Login History
     */
    public function loginHistory(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $login_histories = $this->userTokenRepository
                ->findByAttributes([])
                ->where('expired_at', '>', now())
                ->where('user_id', $user->id)->get();
            return LoginHistoryTransformer::collection($login_histories);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function me(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $token = $this->userTokenRepository->findByAttributes(['access_token' => $user->getFirstApiKey()->access_token]);
            if ($token) {
                $this->userTokenRepository->refreshFor($token);
                $user->refresh();
                DB::commit();
            }
            return [
                'user' => new PublicUserTransformer($user),
                'message' => 'Fetch successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param $user
     * @return void
     */
    private function setPlayerId(Request $request, $user): void
    {
        if (!$request->player_id) return;

        $this->removeOldUserPlayerIDIfInUse($request, $user);

        if (UserDevice::where('player_id', $request->player_id)->where('user_id', $user->id)->exists()) {
            return;
        }


        $user->userDevices()->create($request->only('player_id'));
    }

    /**
     * @param Request $request
     * @return void
     */
    private function removePlayerId(Request $request): void
    {
        $user = $this->getUserFromRequest($request);
        $user->userDevices()->where('user_id', $user->id)->delete();
        $user->save();
    }

    /**
     * @param Request $request
     * @return void
     */
    private function removeOldUserPlayerIDIfInUse(Request $request, $user): void
    {
        UserDevice::wherePlayerId($request->player_id)
            ->where('user_id', '!=', $user->id)
            ->delete();
    }

    private function checkReferCode(mixed $refer_code)
    {
        if ($refer_code == null) return true;

        return $this->referralCodeRepository
            ->where('code', $refer_code)
            ->whereNull('used_at')
            ->exists();
    }

    /**
     * @param RegisterRequest $request
     * @return mixed
     */
    private function checkIfCodeExists(RegisterRequest $request)
    {
        return $this->referralCodeRepository
            ->where('code', $request->refer_code)
            ->whereNull('used_at')->exists();
    }

    /**
     * @param $referer
     * @return void
     */
    private function updateIfSendItNull($referer): void
    {
        if ($referer->sent_at == null) {
            $referer->sent_at = now();
            $referer->save();
        }
    }

    /**
     * @param $referer
     * @param $user
     * @return void
     */
    private function useReferralCode($referer, $user): void
    {
        $referer->update([
            'used_at' => now(),
            'used_user_id' => $user->id
        ]);
    }

    /**
     * @param $referer
     * @return void
     * @throws Exception
     */
    private function createNewReferralCode($referer): void
    {
        $generator = new ReferralCodeGenerator();
        $code = $generator->generate();
        $this->referralCodeRepository->create([
            'user_id' => $referer->user_id,
            'code' => $code
        ]);
    }

    public function verifyPassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = $this->user->findByAttributes(['email' => $validatedData['email']]);
        if (!$user)
            return response()->json(['message' => 'Incorrect Email'], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => Hash::check($validatedData['password'], $user->password)], Response::HTTP_NOT_FOUND);
    }
}
