<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\MarkingKeyLog;
use Modules\Drivisa\Services\MarkingKeyLogService;
use Modules\Drivisa\Transformers\MarkingKeyLogTransformer;
use Modules\Drivisa\Repositories\InstructorRepository;

class MarkingKeyLogController extends ApiBaseController
{
    private $markingKeyLogService;
    private $instructorRepository;

    /**
     * @param InstructorRepository $instructorRepository
     */

    public function __construct(
        MarkingKeyLogService $markingKeyLogService,
        InstructorRepository $instructorRepository
    ) {
        $this->markingKeyLogService = $markingKeyLogService;
        $this->instructorRepository = $instructorRepository;
    }

    public function addMarkingKeyLog(Request $request)
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
            $this->markingKeyLogService->addMarkingKeyLog($request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
