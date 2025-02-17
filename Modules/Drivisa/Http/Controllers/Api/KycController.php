<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\LessonInstructorTransformer;

class KycController extends ApiBaseController
{
    private InstructorRepository $instructorRepository;
    private TraineeRepository $traineeRepository;

    /**
     * @param InstructorRepository $instructorRepository
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        InstructorRepository $instructorRepository,
        TraineeRepository    $traineeRepository
    )
    {
        $this->instructorRepository = $instructorRepository;
        $this->traineeRepository = $traineeRepository;
    }

    public function updateInstructorKYC(Request $request)
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

            $instructor->kyc_verification = Instructor::KYC['InProgress'];
            $instructor->save();

            DB::commit();

            return $this->successMessage("KYC updated");


        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function updateTraineeKYC(Request $request)
    {
        DB::beginTransaction();
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

            $trainee->kyc_verification = Trainee::KYC['InProgress'];
            $trainee->save();

            DB::commit();

            return $this->successMessage("KYC updated");

        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function signAgreement(Request $request)
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

            $instructor->signed_agreement = 1;
            $instructor->signed_at = now();
            $instructor->save();

            DB::commit();

            return $this->successMessage("Agreement Signed Successfully");


        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
