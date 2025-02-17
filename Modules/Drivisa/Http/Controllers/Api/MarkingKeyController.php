<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\MarkingKeyRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\MarkingKeyService;
use Modules\Drivisa\Transformers\MarkingKeyTransformer;

class MarkingKeyController extends ApiBaseController
{
    private $instructorRepository;
    private $markingKeyRepository;
    private $markingKeyService;

    public function __construct(
        MarkingKeyRepository $markingKeyRepository,
        InstructorRepository          $instructorRepository,
        MarkingKeyService     $markingKeyService
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->markingKeyRepository = $markingKeyRepository;
        $this->markingKeyService = $markingKeyService;
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
            $markingKeys = $this->markingKeyRepository->serverPaginationFilteringFor($request);
            return MarkingKeyTransformer::collection($markingKeys);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getMarkingKeys(Request $request)
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

            $markingKeys = $this->markingKeyService->getMarkingKeys($request->trainee_id);
            return response()->json(['data' => $markingKeys], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
