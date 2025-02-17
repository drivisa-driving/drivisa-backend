<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\FinalTestLog;
use Modules\Drivisa\Services\FinalTestLogService;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Transformers\FinalTestLogTransformer;

class FinalTestLogController extends ApiBaseController
{
    private $finalTestLogService;
    private $instructorRepository;

    /**
     * @param InstructorRepository $instructorRepository
     */

    public function __construct(
        FinalTestLogService  $finalTestLogService,
        InstructorRepository $instructorRepository
    )
    {
        $this->finalTestLogService = $finalTestLogService;
        $this->instructorRepository = $instructorRepository;
    }

    public function finalTestLog(Request $request)
    {
        DB::beginTransaction();
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
            $this->finalTestLogService->finalTestLog($request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.final_test_log_added'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
