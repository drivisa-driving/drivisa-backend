<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\StripeCardService;
use Stripe\Stripe;

class StripeCardController extends ApiBaseController
{
    private TraineeRepository $traineeRepository;
    private StripeCardService $stripeCardService;

    /**
     * @param TraineeRepository $traineeRepository
     * @param StripeCardService $stripeCardService
     */
    public function __construct(
        TraineeRepository $traineeRepository,
        StripeCardService $stripeCardService
    )
    {
        $this->traineeRepository = $traineeRepository;
        $this->stripeCardService = $stripeCardService;
    }

    public function add(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($trainee === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.trainee_not_found'));
            }

            if ($trainee->stripe_customer_id === null) {
                $trainee->stripe_customer_id = $this->stripeCardService->createCustomerIdFromStripe($trainee)->id;
                $trainee->save();
            }

            $response = $this->stripeCardService->addCard($trainee->stripe_customer_id, $request->all());
            return \response(['data' => $response]);

        } catch (\Throwable $throwable) {
            dd($throwable->getMessage());
        }
    }

    public function getAll(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($trainee === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.trainee_not_found'));
            }

            if ($trainee->stripe_customer_id === null) {
                return $this->errorMessage(trans('drivisa::drivisa.stripe.customer_id_not_found'));
            }

            $cards = $this->stripeCardService->getAllCards($trainee->stripe_customer_id);

            return \response(['data' => $cards]);
        } catch (\Throwable $throwable) {
            dd($throwable->getMessage());
        }
    }

    public function updateCard(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($trainee === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.trainee_not_found'));
            }

            if ($trainee->stripe_customer_id === null) {
                return $this->errorMessage(trans('drivisa::drivisa.stripe.customer_id_not_found'));
            }

            $card = $this->stripeCardService->updateCard(
                $trainee->stripe_customer_id,
                $request->card_id,
                $request->all()
            );

            return \response(['data' => $card]);
        } catch (\Throwable $throwable) {
            dd($throwable->getMessage());
        }
    }

    public function deleteCard(Request $request, $card_id)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($trainee === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.trainee_not_found'));
            }

            if ($trainee->stripe_customer_id === null) {
                return $this->errorMessage(trans('drivisa::drivisa.stripe.customer_id_not_found'));
            }

            $this->stripeCardService->deleteCard(
                $trainee->stripe_customer_id,
                $card_id
            );

            return \response(['message' => "Card Deleted"]);
        } catch (\Throwable $throwable) {
            dd($throwable->getMessage());
        }
    }
}
