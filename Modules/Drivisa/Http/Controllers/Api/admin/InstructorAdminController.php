<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Traits\DocumentTrait;
use Modules\Drivisa\Services\BDELogService;
use Modules\Drivisa\Services\LessonService;
use Modules\Drivisa\Services\ScheduleService;
use Modules\Drivisa\Services\MarkingKeyService;
use Modules\Drivisa\Entities\VerificationRequest;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Services\MarkingKeyLogService;
use Modules\Drivisa\Services\CompletedLessonService;
use Modules\Drivisa\Events\InstructorAccountRejected;
use Modules\Drivisa\Events\InstructorAccountVerified;
use Modules\Drivisa\Transformers\admin\InstructorAdminTableTransformer;
use Modules\Drivisa\Transformers\PointTransformer;
use Modules\Drivisa\Transformers\ScheduleTransformer;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\MarkingKeyRepository;
use Modules\Drivisa\Http\Requests\SearchLessonsRequest;
use Modules\Drivisa\Transformers\LessonListTransformer;
use Modules\Drivisa\Transformers\MarkingKeyTransformer;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Services\RentalRequestService;
use Modules\Drivisa\Transformers\LessonInstructorTransformer;
use Modules\Drivisa\Transformers\admin\InstructorAdminTransformer;
use Modules\Drivisa\Transformers\InstructorCarRentalRequestTransformer;
use Modules\Drivisa\Transformers\admin\InstructorProfileAdminTransformer;
use Modules\Drivisa\Transformers\WebSchedules\ScheduleNewTransformer;

class InstructorAdminController extends ApiBaseController
{
    use DocumentTrait;

    /**
     * @param LessonService $lessonService
     */
    private $lessonService;

    /**
     * @var InstructorRepository
     */
    private $instructor;

    /**
     * @param ScheduleService $scheduleService
     */
    private $scheduleService;

    /**
     * @param LessonRepository $lessonRepository
     */
    private $lessonRepository;
    /**
     * @param CompletedLessonService $completedLessonService
     */
    private $completedLessonService;

    /**
     * @param MarkingKeyService $markingKeyService
     */
    private $markingKeyService;

    /**
     * @param BDELogService $bdeLogService
     */
    private $bdeLogService;

    /**
     * @param MarkingKeyLogService $markingKeyLogService
     */
    private $markingKeyLogService;

    /**
     * @param MarkingKeyRepository $markingKeyRepository
     */
    private $markingKeyRepository;

    /**
     * @param RentalRequestService $rentalRequestService
     */
    private $rentalRequestService;

    public function __construct(
        InstructorRepository   $instructor,
        LessonService          $lessonService,
        ScheduleService        $scheduleService,
        LessonRepository       $lessonRepository,
        CompletedLessonService $completedLessonService,
        MarkingKeyService      $markingKeyService,
        BDELogService          $bdeLogService,
        MarkingKeyLogService   $markingKeyLogService,
        MarkingKeyRepository   $markingKeyRepository,
        RentalRequestService   $rentalRequestService
    )
    {
        $this->instructor = $instructor;
        $this->lessonService = $lessonService;
        $this->scheduleService = $scheduleService;
        $this->lessonRepository = $lessonRepository;
        $this->completedLessonService = $completedLessonService;
        $this->markingKeyService = $markingKeyService;
        $this->bdeLogService = $bdeLogService;
        $this->markingKeyLogService = $markingKeyLogService;
        $this->markingKeyRepository = $markingKeyRepository;
        $this->rentalRequestService = $rentalRequestService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        try {
            $instructors = $this->instructor->serverPaginationFilteringFor($request);
            if ($request->type == 'table') {
                if ($request->instructor_type == 2) {
                    if($search !='null' && $search !=''){
                        return \response()->json([
                            'data' => InstructorAdminTableTransformer::collection(Instructor::with('user')->where('first_name','LIKE','%'.$search.'%')
                                ->orWhere('last_name','LIKE','%'.$search.'%')
                                ->orWhereHas('user',function ($user) use ($search){
                                    $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                                })->where('signed_agreement', 1)->paginate()),
                            'total' => Instructor::with('user')->where('first_name','LIKE','%'.$search.'%')
                                ->orWhere('last_name','LIKE','%'.$search.'%')
                                ->orWhereHas('user',function ($user) use ($search){
                                    $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                                })->where('signed_agreement', 1)->count(),
                            'type'=>1]);
                    }
                    return \response()->json([
                        'data' => InstructorAdminTableTransformer::collection($this->instructor->where('signed_agreement', 1)->paginate()),
                        'total' => $this->instructor->where('signed_agreement', 1)->count(),
                        'type'=>2]);
                }else if ($request->instructor_type !=='null') {
                        if ($search != 'null' && $search != '') {
                            return \response()->json([
                                'data' => InstructorAdminTableTransformer::collection(Instructor::with('user')->where('first_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                                    ->orWhereHas('user', function ($user) use ($search) {
                                        $user->where('email', 'LIKE', '%' . $search . '%')->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                                    })->where('verified', $request->instructor_type)->paginate()),
                                'total' => Instructor::with('user')->where('first_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                                    ->orWhereHas('user', function ($user) use ($search) {
                                        $user->where('email', 'LIKE', '%' . $search . '%')->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                                    })->where('verified', $request->instructor_type)->count(),
                                'type' => 3]);
                        }
                    return \response()->json([
                        'data' => InstructorAdminTableTransformer::collection($this->instructor->where('verified', $request->instructor_type)->paginate()),
                        'total' => $this->instructor->where('verified', $request->instructor_type)->count(),'type'=>4]);
                }else {
                        if ($search != 'null' && $search != '') {
                            return \response()->json([
                                'data' => InstructorAdminTableTransformer::collection(Instructor::with('user')->where('first_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                                    ->orWhereHas('user', function ($user) use ($search) {
                                        $user->where('email', 'LIKE', '%' . $search . '%')->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                                    })->paginate()),
                                'total' => Instructor::with('user')->where('first_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                                    ->orWhereHas('user', function ($user) use ($search) {
                                        $user->where('email', 'LIKE', '%' . $search . '%')->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                                    })->count(),
                                'type' => 5]);
                        }
                        return \response()->json([
                            'data' => InstructorAdminTableTransformer::collection($this->instructor->paginate()),
                            'total' => $this->instructor->count(), 'type' => 6]);
                }
            }
            return InstructorAdminTransformer::collection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function show(Instructor $instructor)
    {
        try {
            return new InstructorProfileAdminTransformer($instructor);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function details($id)
    {
        try {
            return new InstructorAdminTransformer(Instructor::find($id));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function verifyOrRejectInstructor(Request $request, Instructor $instructor)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor->verificationRequest()->updateOrCreate([
                'status' => $request->verified
                    ? VerificationRequest::STATUS['verified']
                    : VerificationRequest::STATUS['unverified'],
                'verified_by' => $user->id,
                'verified_at' => $request->verified
                    ? Carbon::now()->format('Y-m-d H:i:s')
                    : null,
                'message' => $request->message
            ]);


            $this->updateAllDocumentStatus([
                'entity_id' => $instructor->id,
                'status' => $request->verified ? 'approved' : 'reject',
                'reason' => $request->message
            ]);


            $instructor->update([
                'verified' => $request->verified ? 1 : 0,
                'kyc_verification' => $request->verified ? Instructor::KYC['Approved'] : Instructor::KYC['Rejected']
            ]);
            DB::commit();

            if ($request->verified) {
                event(new InstructorAccountVerified($instructor, $request->message));
                $instructor->update(['verified_at' => now()]);
                return $this->successMessage(trans('drivisa::drivisa.messages.instructor_verified'));
            } else {
                event(new InstructorAccountRejected($instructor, $request->message));
                return $this->successMessage(trans('drivisa::drivisa.messages.instructor_rejected'));
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function instructorSchedules(Request $request)
    {
        try {

            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = Instructor::find($request->instructor_id);

            $workingDays = $this->scheduleService->getSchedule($instructor, $request);
            return ScheduleTransformer::collection($workingDays);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
    public function instructorSchedulesNew(Request $request)
    {
        try {

            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = Instructor::find($request->instructor_id);

            $workingDays = $this->scheduleService->getSchedule($instructor, $request);
            return ScheduleNewTransformer::collection($workingDays);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getPoints($instructor_id)
    {
        try {
            $instructors = Point::where('instructor_id',$instructor_id)->get();

            return PointTransformer::collection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
    public function getInstructorLessonsList(Request $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->lessonService->getInstructorLessonList($instructor, 100);
            return new LessonListTransformer($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function getInstructorLessonsHistory(SearchLessonsRequest $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $data = $request->validated();
            $data['instructor_id'] = $instructor->id;
            $lessons = $this->lessonService->searchLessons($data);
            return LessonInstructorTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getInstructorCompletedLessons(Request $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->completedLessonService->instructorCompletedLessons($instructor);
            return LessonInstructorTransformer::collection($lessons);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function endLesson(Request $request)
    {
        DB::beginTransaction();
        try {
            $lesson = $this->lessonRepository->findByAttributes(['id' => $request->id]);
            $instructor = $this->instructor->findByAttributes(['id' => $lesson->instructor_id]);
            $this->lessonService->updateEndedAt($instructor, $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.ended_at_updated'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getMarkingKeys(Request $request, Lesson $lesson)
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

    public function endBdeLessonByAdmin(Request $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->completedLessonService->endBdeLessonByAdmin($request->all(), $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.success'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getAvailableRequestForInstructor(Request $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$instructor) {
                $message = trans('user::users.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getAvailableRequests($instructor);
            return InstructorCarRentalRequestTransformer::collection($rentalRequests);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getAcceptedRequestForInstructor(Request $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            if (!$instructor) {
                $message = trans('user::users.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $rentalRequests = $this->rentalRequestService->getAcceptedRequests($instructor, $request->all());
            return InstructorCarRentalRequestTransformer::collection($rentalRequests);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function all()
    {
        try {
            return $this->instructor
                ->where('verified', 1)
                ->where('signed_agreement', true)
                ->get();
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
