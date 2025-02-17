<?php


namespace Modules\Drivisa\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Http\Requests\SubscriptionCourseRequest;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\CourseService;
use Modules\Drivisa\Transformers\CourseTransformer;
use Exception;

class CourseController extends ApiBaseController
{
    private $course;
    private $traineeRepository;
    private $courseService;

    public function __construct(CourseRepository  $course,
                                TraineeRepository $traineeRepository,
                                CourseService     $courseService)
    {
        $this->course = $course;
        $this->traineeRepository = $traineeRepository;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        try {
            $courses = $this->course->serverPaginationFilteringFor($request);
            return CourseTransformer::collection($courses);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function show(Request $request, Course $course)
    {
        try {
            return new CourseTransformer($course);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function subscription(SubscriptionCourseRequest $request, Course $course)
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
            $this->courseService->subscription($trainee, $course, $request->validated());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.subscription_created'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTraineeCourses(Request $request)
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
            return CourseTransformer::collection($trainee->user->courses);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function cancelCourse(Request $request, Course $course)
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

            if ($course->status !== Course::STATUS['initiated']) {
                return $this->errorMessage("In Progress Or Completed Course Can't Cancelled", Response::HTTP_BAD_REQUEST);
            }

            $this->courseService->cancelCourse($course, $request->all());
            DB::commit();

            return $this->successMessage("Course Cancelled");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


}