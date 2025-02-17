<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Services\BDELogService;
use Modules\User\Repositories\UserRepository;
use Modules\Drivisa\Services\BdeReportService;
use Modules\Drivisa\Http\Requests\StoreBdeRequest;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\BDELogTransformer;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\TraineeBDELogTransformer;

class BDELogController extends ApiBaseController
{
    public function __construct(
        public BDELogService     $bdeLogService,
        public BdeReportService  $bdeReportService,
        public UserRepository    $user,
        public TraineeRepository $trainee,
        public InstructorRepository $instructor
    ) {
    }

    public function addBde(StoreBdeRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $bdeLog = $this->bdeLogService->addBde($request->all());
            DB::commit();

            return response()->json(['data' => BDELogTransformer::collection($bdeLog)]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function details($id)
    {
        try {
            return new BDELogTransformer(BDELog::find($id));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function traineeBDEDetails($username)
    {
        try {
            $user = $this->user->findByAttributes(['username' => $username]);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $data = $this->bdeReportService->getReport($trainee);
            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLatestBdeLog(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }

            $bdeLog = $this->bdeLogService->getLatestBdeLog($request->lesson_id);
            return response()->json(['data' => $bdeLog]);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
