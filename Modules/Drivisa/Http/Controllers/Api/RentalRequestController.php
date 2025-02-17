<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Http\Requests\RentalRequestRegisterRequest;
use Modules\Drivisa\Http\Requests\RentalRescheduleRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\RentalRequestRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Services\RentalRequestService;
use Modules\Drivisa\Transformers\CarRentalRequestTransformer;
use Modules\Drivisa\Transformers\CarTransformer;
use Modules\Drivisa\Transformers\InstructorCarRentalRequestTransformer;
use Modules\Drivisa\Transformers\LessonTransformer;
use Modules\Drivisa\Notifications\TraineeRoadTestRequestReceivedNotification;
use Modules\Drivisa\Notifications\InstructorRoadTestRequestNotification;

class RentalRequestController extends ApiBaseController
{
    const EXPIRE_PAYMENT_TIME = 24;

    /**
     * @param InstructorRepository $instructor
     * @param LessonRepository $lesson
     * @param TraineeRepository $trainee
     * @param RentalRequestRepository $rentalRequestRepository
     * @param RentalRequestService $rentalRequestService
     */
    public function __construct(
        private InstructorRepository    $instructor,
        private LessonRepository        $lesson,
        private TraineeRepository       $trainee,
        private RentalRequestRepository $rentalRequestRepository,
        private RentalRequestService    $rentalRequestService

    ) {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function allRequests(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->trainee->findByAttributes(['user_id' => $authUser->id]);

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getRequests($trainee, $request->all());

            return CarRentalRequestTransformer::collection($rentalRequests);
        } catch (\Throwable $throwable) {
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param RentalRequestRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RentalRequestRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->trainee->findByAttributes(['user_id' => $authUser->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['id' => $request->instructor_id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->rentalRequestService->register($trainee, $request->all());
            DB::commit();

            return $this->successMessage("Rental Request Registered");
        } catch (\Throwable $throwable) {
            DB::rollBack();
            \Log::error($throwable);
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function paid(Request $request, RentalRequest $rentalRequest)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->trainee->findByAttributes(['user_id' => $authUser->id]);

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['id' => $rentalRequest->instructor_id]);

            if ($rentalRequest->expire_payment_time->lte(now())) {
                $message = "You can't pay a expired car rental request";
                return $this->errorMessage($message, Response::HTTP_FORBIDDEN);
            }

            $lessonIds = $rentalRequest->cancel_lesson_id ? json_decode($rentalRequest->cancel_lesson_id) : null;

            $this->rentalRequestService->cancelLessons($lessonIds);

            $this->rentalRequestService->paid($rentalRequest, $request->all());
            DB::commit();

            return $this->successMessage("Rental Request Successfully Paid");
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function getAvailableRequestForInstructor(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);

            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getAvailableRequests($instructor);

            return InstructorCarRentalRequestTransformer::collection($rentalRequests);
        } catch (\Throwable $throwable) {
            dd($throwable);
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function acceptRentalRequestByInstructor(Request $request, RentalRequest $rentalRequest, $type, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);

            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->rentalRequestService->getConflictingLessons($instructor, $rentalRequest);
            $rentalRequest->update(['cancel_lesson_id' => json_encode($lessons->pluck('id'))]);

            if ($type === 'declined') {

                if ($rentalRequest->is_reschedule_request === 1 && $rentalRequest->reschedule_payment_intent_id !== NULL) {
                    $this->rentalRequestService->refundRoadTestRescheduleFee($rentalRequest);
                }

                //release old working hour for availability
                $this->rentalRequestService->releaseOldWorkingHour($rentalRequest);

                $rentalRequest->update(['status' => RentalRequest::STATUS['declined']]);

                DB::commit();
                $rentalRequest->trainee->user->notify(new TraineeRoadTestRequestReceivedNotification($rentalRequest, $instructor));

                $instructor->user->notify(new InstructorRoadTestRequestNotification($rentalRequest));

                return $this->successMessage("Rental Request Declined");
            } else {
                if ($rentalRequest->is_reschedule_request == 1) {
                    $lesson = $this->lesson->findByAttributes(['id' => $rentalRequest->lesson_id]);
                    $lesson->update([
                        'status' => Lesson::STATUS['rescheduled'],
                        'is_rescheduled' => 1,
                        'rescheduled_payment_id' => $rentalRequest->reschedule_payment_intent_id,
                        'reschedule_time' => now()
                    ]);

                    $rentalRequest->update([
                        'status' => RentalRequest::STATUS['rescheduled']
                    ]);

                    $lessonIds = $rentalRequest->cancel_lesson_id ? json_decode($rentalRequest->cancel_lesson_id) : null;
                    $this->rentalRequestService->cancelLessons($lessonIds);

                    $this->rentalRequestService->createRescheduleLesson($rentalRequest, $lesson);
                } else {
                    $rentalRequest->status = RentalRequest::STATUS[$type];
                }

                $rentalRequest->instructor_id = $instructor->id;
                $rentalRequest->expire_payment_time = Carbon::now()->addHours(self::EXPIRE_PAYMENT_TIME);
                $rentalRequest->save();


                DB::commit();
                $rentalRequest->trainee->user->notify(new TraineeRoadTestRequestReceivedNotification($rentalRequest, $instructor));
                $instructor->user->notify(new InstructorRoadTestRequestNotification($rentalRequest));

                return $this->successMessage("Rental Request Accepted");
            }
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function getAcceptedRequestForInstructor(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);

            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getAcceptedRequests($instructor, $request->all());

            return InstructorCarRentalRequestTransformer::collection($rentalRequests);
        } catch (\Throwable $throwable) {
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function checkConflict(Request $request, RentalRequest $rentalRequest)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);

            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->rentalRequestService->getConflictingLessons($instructor, $rentalRequest);

            return LessonTransformer::collection($lessons);
        } catch (\Throwable $throwable) {
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param RentalRescheduleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reschedule(RentalRescheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->trainee->findByAttributes(['user_id' => $authUser->id]);

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->rentalRequestService->reschedule($trainee, $request->all());
            DB::commit();

            return $this->successMessage("Rental Rescheduled Request Sent Successfully");
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorMessage($throwable->getMessage(), $throwable->getCode());
        }
    }
}
