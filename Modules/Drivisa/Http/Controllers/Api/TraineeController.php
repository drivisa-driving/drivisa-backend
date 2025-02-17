<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Services\StatsService;
use Modules\Drivisa\Services\BookingService;
use Modules\Drivisa\Services\TraineeService;
use Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Modules\Drivisa\Events\DocumentUploadedEvent;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Services\ResetPickDropService;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\TraineeTransformer;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\User\Transformers\UserProfileTransformer;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Http\Requests\ResetPickDropRequest;
use Modules\Drivisa\Repositories\WorkingHourRepository;
use Modules\Drivisa\Transformers\InstructorTransformer;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\UploadDocumentRequest;
use Modules\Drivisa\Transformers\TraineeInfoTransformer;
use Modules\Drivisa\Http\Requests\GetExtraDistanceRequest;
use Modules\Drivisa\Transformers\TraineeProfileTransformer;
use Modules\Drivisa\Http\Requests\StoreBookingLessonRequest;
use Modules\Drivisa\Http\Requests\UpdateTraineeProfileRequest;
use Modules\Drivisa\Http\Requests\UploadSingleDocumentRequest;
use Modules\Drivisa\Transformers\InstructorProfileTransformer;
use Modules\Drivisa\Transformers\TraineeEvaluationTransformer;
use Modules\Drivisa\Http\Requests\StoreBookingLessonByCourseRequest;

class TraineeController extends ApiBaseController
{
    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var TraineeRepository
     */
    private $trainee;
    private $instructor;

    /**
     * @var BookingService
     */
    private $bookingService;

    private $traineeService;
    private WorkingHourRepository $workingHourRepository;
    private StatsService $statsService;
    private ResetPickDropService $resetPickDropService;


    /**
     * @param UserRepository $user
     * @param TraineeRepository $trainee
     * @param InstructorRepository $instructor
     * @param TraineeService $traineeService
     * @param BookingService $bookingService
     * @param WorkingHourRepository $workingHourRepository
     * @param StatsService $statsService
     * @param ResetPickDropService $resetPickDropService
     */
    public function __construct(
        UserRepository        $user,
        TraineeRepository     $trainee,
        InstructorRepository  $instructor,
        TraineeService        $traineeService,
        BookingService        $bookingService,
        WorkingHourRepository $workingHourRepository,
        StatsService          $statsService,
        ResetPickDropService  $resetPickDropService
    ) {
        $this->user = $user;
        $this->trainee = $trainee;
        $this->instructor = $instructor;
        $this->traineeService = $traineeService;
        $this->bookingService = $bookingService;
        $this->workingHourRepository = $workingHourRepository;
        $this->statsService = $statsService;
        $this->resetPickDropService = $resetPickDropService;
    }

    /**
     * Trainee updating his/her profile
     * @param UpdateTraineeProfileRequest $request
     * @return JsonResponse|UserProfileTransformer
     */
    public function updateTraineeProfile(UpdateTraineeProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->user->update($user, [
                'first_name' => $request->get('first_name', $user->first_name),
                'last_name' => $request->get('last_name', $user->last_name),
                'address' => $request->get('address', $user->address),
                'phone_number' => $request->get('phone_number', $user->phone_number),
                'city' => $request->get('city', $user->city),
                'postal_code' => $request->get('postal_code', $user->postal_code),
                'province' => $request->get('province', $user->province),
            ]);

            $user->street = $request->get('street', $user->street);
            $user->unit_no = $request->get('unit_no', $user->unit_no);
            $user->save();

            $this->trainee->update($trainee, $request->all());
            DB::commit();
            return new TraineeTransformer($trainee);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * booking lesson in specific working hour
     * The request should contains workinghours' ids,
     * @param StoreBookingLessonRequest $request
     * @return array|Application|Translator|JsonResponse|string|null
     */
    public function BookingLesson(StoreBookingLessonRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->bookingService->bookingLesson($trainee, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.booking_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function bookingLessonByCredit(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $workingHour = $this->workingHourRepository->find($request->lesson_id);

            if ($request->bde_lesson === true) {
                $stats = $this->statsService->getStatsByType($user, "BDE", true);
                if ($stats['data']['remaining_hours'] < 1 || $stats['data']['remaining_hours'] < $workingHour->duration) {
                    return $this->errorMessage("BDE Credit is Low", Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $stats = $this->statsService->getStatsByType($user, "Package", true);
                if ($stats['data']['remaining_hours'] < 1 || $stats['data']['remaining_hours'] < $workingHour->duration) {
                    return $this->errorMessage("Credit is Low", Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }


            $this->bookingService->bookingLessonByCourse($trainee, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.booking_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function bookingLessonByCourse(StoreBookingLessonByCourseRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->bookingService->bookingLessonByCourse($trainee, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.booking_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get authenticated trainee
     * @param Request $request
     * @return JsonResponse|TraineeTransformer
     */
    public function me(Request $request)
    {
        $authUser = $this->getUserFromRequest($request);
        if (!$authUser) {
            return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
        }
        $trainee = $this->trainee->findByAttributes(['user_id' => $authUser->id]);
        if (!$trainee) {
            $message = trans('drivisa::drivisa.messages.trainee_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        return new TraineeTransformer($trainee);
    }

    /**
     * Get instructor by username
     * @param $username
     * @return JsonResponse|InstructorProfileTransformer
     */
    public function findByUsername(Request $request, $username)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $user = $this->user->findByAttributes(['username' => $username]);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            $hasLesson = $trainee->lessons()->first();
            if (!$hasLesson) {
                throw new AuthorizationException();
            }
            return new TraineeProfileTransformer($trainee);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getProfileInfo(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if (!$trainee) {
            $message = trans('drivisa::drivisa.messages.trainee_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        $traineeInfo = $this->traineeService->getProfileInfo($trainee);
        return new TraineeInfoTransformer($traineeInfo);
    }

    public function uploadSingleDocuments(UploadSingleDocumentRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->traineeService->uploadSingleDocument($authUser, $request->all());

            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.documents_uploaded'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function uploadDocuments(UploadDocumentRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->traineeService->uploadDocuments($authUser, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.documents_uploaded'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getDocuments(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
        }
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if (!$trainee) {
            $message = trans('drivisa::drivisa.messages.instructor_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        return DocumentTransformer::collection($trainee->files);
    }

    public function getAdditionalPrice(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['id' => $request->instructor_id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->bookingService->getAdditionalPrice($instructor, $request->all());
        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }


    /**
     * Get instructor by username
     * @param $username
     * @return JsonResponse|InstructorProfileTransformer
     */
    public function getTraineeEvaluation(Request $request, $username)
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
            $user = $this->user->findByAttributes(['username' => $username]);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $hasLesson = $trainee->lessons()->where('instructor_id', $instructor->id)->first();
            if (!$hasLesson) {
                throw new AuthorizationException();
            }
            return new TraineeEvaluationTransformer($trainee);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeEvaluationByTrainee(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if (!$trainee) {
            $message = trans('drivisa::drivisa.messages.trainee_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        return new TraineeEvaluationTransformer($trainee);
    }

    public function getRecentInstructors(Request $request)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
        }
        $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
        if (!$trainee) {
            $message = trans('drivisa::drivisa.messages.instructor_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => InstructorTransformer::collection($this->traineeService->getAllRecentInstructors($trainee))->map(function ($instructor) use ($trainee) {
            return new InstructorTransformer($instructor, $trainee);
        })]);
    }

    public function getExtraDistance(GetExtraDistanceRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return response()->json(['data' => $this->resetPickDropService->getExtraDistance($request->validated())]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function resetPickDrop(ResetPickDropRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->resetPickDropService->resetPickDrop($request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.reset_pick_drop'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
