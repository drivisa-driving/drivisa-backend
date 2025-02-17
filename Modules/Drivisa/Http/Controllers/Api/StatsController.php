<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\StatsService;
use Modules\Drivisa\Transformers\PurchaseTransformer;

class StatsController extends ApiBaseController
{
    private StatsService $statsService;
    private TraineeRepository $traineeRepository;

    /**
     * @param StatsService $statsService
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        StatsService      $statsService,
        TraineeRepository $traineeRepository
    )
    {
        $this->statsService = $statsService;
        $this->traineeRepository = $traineeRepository;
    }

    public function getCourseStatsByType(Request $request, $type = 'BDE')
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->statsService->getStatsByType($user, $type);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function missingInfo(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'data' => [
                    'document_submitted' => $this->documentSubmitted($trainee),
                    'profile_finished' => $this->profileFinished($trainee)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    private function documentSubmitted($trainee): bool
    {
        return $trainee->user->files->count() >= 2;
    }

    private function profileFinished($trainee): bool
    {
        return $trainee->bio
            && $trainee->user->phone_number
            && $trainee->user->city
            && $trainee->user->postal_code;
    }
}
