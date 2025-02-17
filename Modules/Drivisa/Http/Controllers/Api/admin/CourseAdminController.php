<?php


namespace Modules\Drivisa\Http\Controllers\Api\admin;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\FinalTestResult;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Http\Requests\StoreCourseRequest;
use Modules\Drivisa\Http\Requests\UpdateCourseRequest;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\MarkingKeyRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\CompletedLessonService;
use Modules\Drivisa\Services\FinalTestLogService;
use Modules\Drivisa\Transformers\CourseTransformer;
use Modules\Drivisa\Transformers\MarkingKeyTransformer;
use Modules\Drivisa\Transformers\admin\CourseAdminTransformer;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class CourseAdminController extends ApiBaseController
{

    private $course;
    private $markingKeyRepository;
    private $lesson;
    private $completedLessonService;
    private $finalTestLogService;
    private $user;
    private $trainee;

    public function __construct(
        CourseRepository $course,
        MarkingKeyRepository $markingKeyRepository,
        LessonRepository $lesson,
        CompletedLessonService $completedLessonService,
        FinalTestLogService $finalTestLogService,
        UserRepository $user,
        TraineeRepository $trainee
    ) {
        $this->course = $course;
        $this->markingKeyRepository = $markingKeyRepository;
        $this->lesson = $lesson;
        $this->completedLessonService = $completedLessonService;
        $this->finalTestLogService = $finalTestLogService;
        $this->user = $user;
        $this->trainee = $trainee;
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

    public function store(StoreCourseRequest $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            $data = $request->validated();
            $data['created_by'] = $user->id;
            $this->course->create($data);
            $message = trans('drivisa::drivisa.messages.course_created');
            return $this->successMessage($message);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {
            $data = $request->validated();
            $this->course->update($course, $data);
            $message = trans('drivisa::drivisa.messages.course_updated');
            return $this->successMessage($message);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function delete(Request $request, Course $course)
    {
        try {
            $this->course->destroy($course);
            $message = trans('drivisa::drivisa.messages.course_deleted');
            return $this->successMessage($message);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function bdeList()
    {
        try {
            $page = \request('page')-1;
            $per_page = \request('per_page');
            $trainees=[];
            if(request('type') !='null' && request('type') !=''){
                if(request('type')==1) {
                    $trainees = $this->trainee
                        ->where('verified', 1)
                        ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                        ->where('licence_issued',0)
                        ->with(['bdeLog.finalTestResult'])
                        ->get();
                    $sortedTrainees = $trainees->sortByDesc(function ($trainee) {
                        return $trainee->bdeLog->max(function ($bdeLog) {
                            return $bdeLog->finalTestResult->max('created_at');
                        });
                    });
                    $collection = CourseAdminTransformer::collection($sortedTrainees)->toResponse(app('request'));
                    $data=  collect($collection->getData()->data)->filter(function ($trainee) {
                            return $trainee->total_hours > $trainee->completed_hours && $trainee->licence_issued===0;
                    })->values();
                    return \response(['data'=>$data->slice($page*$per_page,$per_page)->values(),'total'=>$data->count()]);
                }else if(request('type')==2) {
                    $trainees = $this->trainee
                        ->where('verified', 1)
                        ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                        ->where('licence_issued',0)
                        ->with(['bdeLog.finalTestResult'])
                        ->get();
                    $sortedTrainees = $trainees->sortByDesc(function ($trainee) {
                        return $trainee->bdeLog->max(function ($bdeLog) {
                            return $bdeLog->finalTestResult->max('created_at');
                        });
                    });
                    $collection = CourseAdminTransformer::collection($sortedTrainees)->toResponse(app('request'));
                    $data=  collect($collection->getData()->data)->filter(function ($trainee) {
                        return $trainee->total_hours === $trainee->completed_hours && $trainee->licence_issued===0;
                    })->values();
                    return \response(['data'=>$data->slice($page*$per_page,$per_page)->values(),'total'=>$data->count()]);
                }else if(request('type')==3) {
                    $trainees = $this->trainee
                        ->where('verified', 1)
                        ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                        ->where('licence_issued','<>',0)
                        ->with(['bdeLog.finalTestResult'])
                        ->paginate();
                    $sortedTrainees = $trainees->sortByDesc(function ($trainee) {
                        return $trainee->bdeLog->max(function ($bdeLog) {
                            return $bdeLog->finalTestResult->max('created_at');
                        });
                    });

                    return \response(['data'=>CourseAdminTransformer::collection($sortedTrainees),'total'=>$this->trainee
                        ->where('verified', 1)
                        ->where('licence_issued','<>',0)
                        ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                        ->with(['bdeLog.finalTestResult'])->count()]);
                }

            }else {
                $trainees = Cache::remember('bde_course_trainees', $minutes = 10, function () {
                    return $this->trainee
                        ->where('verified', 1)
                        ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                        ->with(['bdeLog.finalTestResult'])
                        ->paginate();
                });
                $sortedTrainees = $trainees->sortByDesc(function ($trainee) {
                    return $trainee->bdeLog->max(function ($bdeLog) {
                        return $bdeLog->finalTestResult->max('created_at');
                    });
                });

                return \response(['data'=>CourseAdminTransformer::collection($sortedTrainees),'total'=>$this->trainee
                    ->where('verified', 1)
                    ->whereHas('user.courses', fn($course) => $course->where('type', Course::TYPE['BDE']))
                    ->with(['bdeLog.finalTestResult'])->count()]);
            }

        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function licenceIssued(Request $request, Trainee $trainee)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee->licence_issued = 1;
            $trainee->save();
            DB::commit();

            $message = trans('drivisa::drivisa.messages.licence_issued_to_trainee');
            return $this->successMessage($message);
        } catch (Exception $e) {
            DB::rollback();
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

            $markingKeys = $this->markingKeyRepository->serverPaginationFilteringFor($request);
            return MarkingKeyTransformer::collection($markingKeys);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addMarkingKeys(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lesson = $this->lesson->findByAttributes(['no' => $request->lesson_no]);
            if (!$lesson) {
                throw new Exception('Lesson not found', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($lesson->status != Lesson::STATUS['completed']) {
                throw new Exception('Lesson not completed yet', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $isBdeLogExists = BDELog::where('lesson_id', $lesson->id)->first();
            if ($isBdeLogExists) {
                throw new Exception('BDE log for this lesson is already created', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->completedLessonService->endBdeLessonByAdmin($request->all(), $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.success'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addOrUpdateFinalTestLog(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $user = $this->user->findByAttributes(['username' => $request->username]);
            if (!$user) {
                $message = trans('drivisa::drivisa.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $trainee = Trainee::where('user_id', $user->id)->first();

            $this->finalTestLogService->addOrUpdateFinalTestLog($trainee, $request->all());
            DB::commit();

            $message = trans('drivisa::drivisa.messages.final_test_log_added');
            return $this->successMessage($message);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
