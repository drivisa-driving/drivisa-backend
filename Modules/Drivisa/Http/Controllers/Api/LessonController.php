<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Services\CourseService;
use Modules\Drivisa\Services\LessonService;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\LessonTransformer;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Http\Requests\SearchLessonsRequest;
use Modules\Drivisa\Transformers\InstructorTransformer;
use Modules\Drivisa\Transformers\LessonListTransformer;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\LessonTraineeTransformer;
use Modules\Drivisa\Transformers\TraineeLastTripTransformer;
use Modules\Drivisa\Transformers\TraineeProgressTransformer;
use Modules\Drivisa\Http\Requests\CreateLessonByAdminRequest;
use Modules\Drivisa\Http\Requests\UpdateTraineeLessonRequest;
use Modules\Drivisa\Transformers\LessonInstructorTransformer;
use Modules\Drivisa\Transformers\RescheduleLessonTransformer;
use Modules\Drivisa\Transformers\SearchInstructorTransformer;
use Modules\Drivisa\Transformers\InstructorProfileTransformer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use LDAP\Result;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Http\Requests\UpdateInstructorLessonRequest;
use Modules\Drivisa\Transformers\InstructorAvailabilityTransformer;
use Modules\Drivisa\Notifications\InstructorTraineeTrainingNotification;
use Modules\Drivisa\Notifications\InstructorCancelLessonTraineeNotification;
use Modules\Drivisa\Notifications\TraineeCancelLessonInstructorNotification;
use Modules\Drivisa\Notifications\InstructorTraineeLessonRescheduledNotification;

class LessonController extends ApiBaseController
{
    private $lessonService;
    private $courseService;
    private $instructorRepository;
    private $traineeRepository;
    private LessonRepository $lessonRepository;

    /**
     * @param LessonService $lessonService
     * @param CourseService $courseService
     * @param InstructorRepository $instructorRepository
     * @param TraineeRepository $traineeRepository
     * @param LessonRepository $lessonRepository
     */
    public function __construct(
        LessonService        $lessonService,
        CourseService        $courseService,
        InstructorRepository $instructorRepository,
        TraineeRepository    $traineeRepository,
        LessonRepository     $lessonRepository
    ) {
        $this->lessonService = $lessonService;
        $this->courseService = $courseService;
        $this->instructorRepository = $instructorRepository;
        $this->traineeRepository = $traineeRepository;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * Get instructor lessons
     * @param SearchLessonsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getInstructorLessons(SearchLessonsRequest $request)
    {
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
        $data = $request->validated();
        $data['instructor_id'] = $instructor->id;
        $lessons = $this->lessonService->searchLessons($data);
        return LessonInstructorTransformer::collection($lessons);
    }

    public function getInstructorHistory(Request $request)
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
            $lessons = $this->lessonService->getInstructorHistory($instructor, $request->all());
            return LessonInstructorTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getInstructorLesson(Request $request, Lesson $lesson)
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
            $lessons = $this->lessonService->getInstructorLesson($instructor, $lesson);
            return new LessonInstructorTransformer($lessons);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get trainee lessons
     * @param SearchLessonsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getTraineeLessons(SearchLessonsRequest $request)
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
            $data = $request->validated();
            $data['trainee_id'] = $trainee->id;
            $lessons = $this->lessonService->searchLessons($data);
            return LessonTraineeTransformer::collection($lessons);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function cancelLessonByTrainee(Request $request, Lesson $lesson)
    {
        $request->validate([
            'reason' => 'required'
        ]);
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

            $this->lessonService->cancelLesson('trainee', $lesson, $request->all());
            DB::commit();
            return $this->successMessage('Your lesson has been Cancelled. Money will be  refunded after deducting fees', 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLessonsListForInstructor(Request $request)
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

            $lessons = $this->lessonService->getInstructorLessonList($instructor);
            return new LessonListTransformer($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLessonsListForTrainee(Request $request)
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

            $lessons = $this->lessonService->getTraineeLessonList($trainee);
            return new LessonListTransformer($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getPastLessonsListForTrainee(Request $request)
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

            $lessons = $this->lessonService->getTraineePastLessonList($trainee, $request->all());
            return LessonTraineeTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function cancelLessonByInstructor(Request $request, Lesson $lesson)
    {
        $request->validate([
            'reason' => 'required'
        ]);
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

            $lessonStartDateTime = Carbon::parse($lesson->start_at);
            $currentDateTime = Carbon::now()->addMinutes(5);

            if ($lessonStartDateTime->lte(now()) && $currentDateTime->lessThan($lessonStartDateTime)) {
                $message = "Please note! You can only cancel the lesson After 15 minutes from its start in case the student does not show up";
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $message = $currentDateTime->greaterThan($lessonStartDateTime) ? "The lesson has been canceled successfully. Compensation has been inititated" : 'Lesson Cancelled';

            $currentDateTime->greaterThan($lessonStartDateTime) ? $this->lessonService->cancelLessonWhenTraineeNotAvailable($lesson) : $this->lessonService->cancelLesson('instructor', $lesson, $request->all());
            DB::commit();
            return $this->successMessage($message, 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function cancelLessonOnTraineeNotAvailable(Request $request, Lesson $lesson)
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

            $lessonStartDateTime = Carbon::parse($lesson->start_at);
            $currentDateTime = Carbon::now()->addMinutes(15);

            if ($currentDateTime->lessThan($lessonStartDateTime)) {
                $message = "Please wait At least 15 min! If Trainee not reach than you can cancel Lesson";
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->lessonService->cancelLessonWhenTraineeNotAvailable('instructor', $lesson, $request->all());
            DB::commit();
            return $this->successMessage('Lesson Cancelled', 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function getTraineeLesson(Request $request, Lesson $lesson)
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
            $lesson = $this->lessonService->getTraineeLesson($trainee, $lesson);
            return new LessonTraineeTransformer($lesson);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update lesson's started at
     * @param Request $request
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function updateStartedAt(Request $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if ($lesson->status == Lesson::STATUS['canceled']) {
                $message = "Lesson Already Cancelled";
                return $this->errorMessage($message, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->lessonService->updateStartedAt($instructor, $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.started_at_updated'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update lesson's ended at
     * @param Request $request
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function updateEndedAt(Request $request, Lesson $lesson)
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

            if ($lesson->status == Lesson::STATUS['canceled']) {
                $message = "Lesson Already Cancelled";
                return $this->errorMessage($message, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->lessonService->updateEndedAt($instructor, $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.ended_at_updated'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    /**
     * update lesson's instructor note and instructor evaluation
     * @param UpdateInstructorLessonRequest $request
     * @param Lesson $lesson
     * @return JsonResponse|LessonTransformer
     */
    public function updateInstructorEvaluation(UpdateInstructorLessonRequest $request, Lesson $lesson)
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
            $this->lessonService->updateInstructorEvaluation($instructor, $lesson, $request->validated());
            DB::commit();
            return new LessonInstructorTransformer($lesson);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * update lesson's trainee note and trainee evaluation
     * @param UpdateTraineeLessonRequest $request
     * @param Lesson $lesson
     * @return JsonResponse|LessonTransformer
     */
    public function updateTraineeEvaluation(UpdateTraineeLessonRequest $request, Lesson $lesson)
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
            $this->lessonService->updateTraineeEvaluation($trainee, $lesson, $request->validated());
            DB::commit();
            return new LessonTraineeTransformer($lesson);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLastTrip(Request $request)
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
            $lesson = $this->lessonService->getLastTrip($trainee);

            if (!$lesson) {
                $message = "No Last Trip Found";
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return new TraineeLastTripTransformer($lesson);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function availableForReschedule(Request $request)
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
            $lessons = $this->lessonService->availableForReschedule($trainee);

            return new RescheduleLessonTransformer($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function reschedule(Request $request)
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
            $this->lessonService->reschedule($request->all(), $trainee);

            DB::commit();

            return $this->successMessage("Your lesson rescheduled to new time");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getInstructorAvailability(Request $request, Lesson $lesson)
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
            $instructor = $this->lessonService->getInstructorAvailability($lesson);

            $workingHour = WorkingHour::where('id', $lesson->working_hour_id)->first();
            if (!$workingHour) {
                throw new Exception('Working Hour Not Found', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $point = $workingHour->point;

            return (new InstructorAvailabilityTransformer($instructor))->setDuration($lesson->duration)->setPoint($point);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getProgress(SearchLessonsRequest $request)
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

            $last_lesson = $trainee->lessons()
                ->whereNotNull('ended_at')
                ->latest("ended_at")
                ->first();

            $lessons = $trainee->lessons()
                ->whereNotNull("instructor_evaluation")
                ->get()
                ->pluck('instructor_evaluation')
                ->toArray();

            $evals = array_map(function ($lesson) {
                return json_decode($lesson, true);
            }, $lessons);

            $result = [];
            foreach ($evals as $eval) {
                foreach ($eval as $single) {

                    if ($single['points'] == 0) continue;

                    $value = $points = 0;
                    $intValue = intval($single['value']);
                    $points += $single['points'];
                    $value += $intValue;

                    $result[$single['id']]['question'] = $single['title'];
                    $result[$single['id']]['percentage'] = ($value / $points) * 100;
                }
            }
            $data = [
                'last_instructor' => new InstructorProfileTransformer($last_lesson->instructor),
                'evaluations' => array_values($result)
            ];
            return response(['data' => $data]);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function notifyTrainee(Request $request, Lesson $lesson)
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
            $trainee = $lesson->trainee;
            $trainee->user->notify(new InstructorTraineeTrainingNotification($lesson));

            return $this->successMessage("Training Notification Sent");
        } catch (Exception $e) {
        }
    }

    public function refundChoice(Request $request, Lesson $lesson)
    {
        $request->validate([
            'refund_choice' => 'required'
        ]);

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

            $cancelledLesson = $lesson->lessonCancellation;

            if ($cancelledLesson->is_refunded == 0) {
                if ($request->refund_choice == 1) {

                    // Calculate Duration
                    $start_at = Carbon::parse($lesson->start_at);
                    $end_at = Carbon::parse($lesson->end_at);
                    $getDuration = $start_at->diffInHours($end_at);
                    $data = [
                        'user_id' => $lesson->trainee->user_id,
                        'duration' => $getDuration,
                    ];

                    $this->courseService->refundCredit($data);
                    $cancelledLesson->update([
                        'refund_choice' => $request->refund_choice,
                        'is_refunded' => 1
                    ]);

                    DB::commit();
                    return $message = trans('drivisa::drivisa.messages.credit_refunded_successfully');
                } else {
                    $this->lessonService->cancelLessonByInstructor($lesson, $cancelledLesson);
                    $cancelledLesson->update([
                        'refund_choice' => $request->refund_choice,
                        'is_refunded' => 1
                    ]);
                    DB::commit();
                    return $message = trans('drivisa::drivisa.messages.refunded_successfully');
                }
            } else {
                return $message = trans('drivisa::drivisa.messages.already_refunded');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get instructor lessons
     * @param SearchLessonsRequest $request
     */
    public function todayLessonsList(SearchLessonsRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $data = $request->validated();
            return $this->lessonService->todayLessonsList($data);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function createLessonByAdmin(CreateLessonByAdminRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = $this->traineeRepository->findByAttributes(['id' => $request->trainee_id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->lessonService->createLessonByAdmin($user, $trainee, $request->all());
            DB::commit();
            return $message = trans('drivisa::drivisa.messages.lesson_created');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLessonsListCreatedByAdmin(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->lessonRepository->where('created_by', $user->id)
                ->orderByRaw('DATE(start_at) DESC')
                ->orderBy('start_time')
                ->get();
            return LessonTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function transferToInstructor(Request $request, Lesson $lesson, Instructor $instructor)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $transfer = $this->lessonService->createTransfer($lesson, $instructor);
            $lesson->update(['transfer_id' => $transfer->id]);
            DB::commit();
            return $message = trans('drivisa::drivisa.messages.transfer_created');
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
