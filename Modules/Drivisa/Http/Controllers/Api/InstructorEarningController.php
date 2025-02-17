<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\EarningService;

class InstructorEarningController extends ApiBaseController
{
    public function __construct(
        public EarningService       $earningService,
        public InstructorRepository $instructor)
    {

    }

    /**
     * Connect stripe account to instructor
     * @param Request $request
     * @return JsonResponse
     */
    public function getEarningBreakDown(Request $request)
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
            $breakdown = $this->earningService->getEarningBreakDown($instructor, $request->all());
            return \response()->json(['data' => $breakdown]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
