<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\FinalTestResult;
use Modules\Drivisa\Services\FinalTestKeyService;
use Modules\Drivisa\Repositories\InstructorRepository;

class FinalTestKeyController extends ApiBaseController
{
    private $finalTestKeyService;
    private $instructorRepository;

    /**
     * @param InstructorRepository $instructorRepository
     */

    public function __construct(
        FinalTestKeyService $finalTestKeyService,
        InstructorRepository $instructorRepository
    ) {
        $this->finalTestKeyService = $finalTestKeyService;
        $this->instructorRepository = $instructorRepository;
    }

    public function finalTestKeys(Request $request)
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
            $data  =  $this->finalTestKeyService->finalTestKeys($request->all());
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
