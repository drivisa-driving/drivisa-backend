<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\GetStripeEarningsRequest;
use Modules\Drivisa\Http\Requests\StoreStripeAccountRequest;
use Modules\Drivisa\Http\Requests\UpdateStripeAccountRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\StripeService;
use Modules\Drivisa\Transformers\EarningTransformer;
use Modules\Drivisa\Transformers\StripeAccountTransformer;
use Modules\Drivisa\Transformers\WeeklyEarningTransformer;

class StripeController extends ApiBaseController
{
    /**
     * @var InstructorRepository
     */
    private $instructor;
    private $stripeService;

    public function __construct(InstructorRepository $instructor,
                                StripeService        $stripeService)
    {
        $this->instructor = $instructor;
        $this->stripeService = $stripeService;

    }

    /**
     * Get instructor's stripe account
     * @param Request $request
     * @return JsonResponse|StripeAccountTransformer
     */
    public function getStripeAccount(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $strinpeAccount = $this->stripeService->getStripeAccount($instructor);
            return new StripeAccountTransformer($strinpeAccount);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Connect stripe account to instructor
     * @param Request $request
     * @return JsonResponse
     */
    public function connectStripeAccount(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return $this->stripeService->connectStripeAccount($instructor, $request->all());
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Connect stripe account to instructor
     * @param Request $request
     * @return JsonResponse
     */
    public function connectBankAccount(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $account = $this->stripeService->addBankAccount($instructor, $request->only('token'));

            if ($account) {
                return $instructor->stripeAccount->update([
                    'account_number' => $request->account_number,
                    'account_holder_name' => $account->account_holder_name,
                    'account_holder_type' => $account->account_holder_type,
                    'country' => $account->country,
                    'currency' => $account->currency,
                    'fingerprint' => $account->fingerprint,
                    'last4' => $account->last4,
                    'routing_number' => $account->routing_number,
                    'status' => $account->status
                ]);
            }

            return $this->errorMessage('Unable to add bank account');

        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Connect stripe account to instructor
     * @param Request $request
     * @return JsonResponse
     */
    public function updateConnectedStripeAccount(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return $this->stripeService->updateConnectedStripeAccount($instructor, $request->all());
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete instructor's connected stripe account
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConnectedStripeAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);

            $this->stripeService->deleteConnectedStripeAccount($instructor);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.account_deleted'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create stripe account to instructor
     * @param StoreStripeAccountRequest $request
     * @return JsonResponse
     */
    public function createStripeAccount(StoreStripeAccountRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->stripeService->createStripeAccount($instructor, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.account_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete instructor's stripe account
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteStripeAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);

            $this->stripeService->deleteStripeAccount($instructor);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.account_deleted'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function updateStripeAccount(UpdateStripeAccountRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $strinpeAccount = $this->stripeService->updateStripeAccount($instructor, $request->validated());
            DB::commit();
            return new StripeAccountTransformer($strinpeAccount);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getEarnings(GetStripeEarningsRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $earnings = $this->stripeService->getEarnings($instructor);
            return new EarningTransformer($earnings);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getWeeklyEarnings(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $earnings = $this->stripeService->getEarnings($instructor);
            return new WeeklyEarningTransformer($earnings);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


}