<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;
use Modules\Drivisa\Transformers\EvaluationIndicatorTransformer;
use Modules\Drivisa\Services\EvaluationIndicatorService;

class EvaluationIndicatorController extends ApiBaseController
{
    private $instructorRepository;
    private $evaluationIndicatorRepository;
    private $evaluationIndicatorService;

    public function __construct(
        EvaluationIndicatorRepository $evaluationIndicatorRepository,
        InstructorRepository          $instructorRepository,
        EvaluationIndicatorService    $evaluationIndicatorService,
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->evaluationIndicatorRepository = $evaluationIndicatorRepository;
        $this->evaluationIndicatorService = $evaluationIndicatorService;
    }

    public function index(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $evaluations = $this->evaluationIndicatorRepository->allWithBuilder()->orderBy('order')->get();
            return EvaluationIndicatorTransformer::collection($evaluations);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getEvaluations(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $evaluations = $this->evaluationIndicatorService->getEvaluations($request->trainee_id);
            return response()->json(['data' => $evaluations], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
