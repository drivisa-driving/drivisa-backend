<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Services\StripeService;
use Illuminate\Contracts\Support\Renderable;
use Modules\Drivisa\Services\EarningService;
use Modules\Drivisa\Transformers\EarningTransformer;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\WeeklyEarningTransformer;
use Modules\Drivisa\Http\Requests\GetStripeEarningsRequest;

class InstructorAdminEarningController extends ApiBaseController
{

    private $stripeService;

    public function __construct(public EarningService $earningService, StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Connect stripe account to instructor
     * @param Request $request
     * @return JsonResponse
     */

    public function getEarnings(GetStripeEarningsRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = Instructor::find($request->id);
            $earnings = $this->stripeService->getEarnings($instructor);
            return new EarningTransformer($earnings);
        } catch (\Exception $e) {
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
            $instructor = Instructor::find($request->id);
            $earnings = $this->stripeService->getEarnings($instructor);
            return new WeeklyEarningTransformer($earnings);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function getEarningBreakDown(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = Instructor::find($request->id);
            $breakdown = $this->earningService->getEarningBreakDown($instructor, $request->all());
            return \response()->json(['data' => $breakdown]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
