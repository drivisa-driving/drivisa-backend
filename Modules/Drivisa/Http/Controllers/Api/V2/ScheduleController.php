<?php

namespace Modules\Drivisa\Http\Controllers\Api\V2;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\Drivisa\Services\ScheduleService;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\V2\ScheduleTransformer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduleController extends ApiBaseController
{
    /**
     * @var ScheduleService
     */
    protected $scheduleService;
    protected $instructorRepository;

    public function __construct(
        ScheduleService        $scheduleService,
        InstructorRepository   $instructorRepository,
    ) {
        $this->scheduleService = $scheduleService;
        $this->instructorRepository = $instructorRepository;
    }

    /**
     * Get instructor's schedule
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $authUser->id]);
            if ($instructor === null) {
                return $this->errorMessage(trans('drivisa::drivisa.messages.instructor_not_found'));
            }
            $workingDays = $this->scheduleService->getSchedule($instructor, $request);
            return ScheduleTransformer::collection($workingDays);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
