<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\VerificationRequest;
use Modules\Drivisa\Events\DocumentUploadedEvent;
use Modules\Drivisa\Http\Requests\SearchInstructorsRequest;
use Modules\Drivisa\Http\Requests\UpdateInstructorProfileRequest;
use Modules\Drivisa\Http\Requests\UploadDocumentRequest;
use Modules\Drivisa\Http\Requests\UploadSingleDocumentRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\InstructorService;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\Drivisa\Transformers\InstructorCurrentLessonTransformer;
use Modules\Drivisa\Transformers\InstructorInfoTransformer;
use Modules\Drivisa\Transformers\InstructorProfileAppTransformer;
use Modules\Drivisa\Transformers\InstructorProfileTransformer;
use Modules\Drivisa\Transformers\SearchInstructorAppCollection;
use Modules\Drivisa\Transformers\SearchInstructorAppTransformer;
use Modules\Drivisa\Transformers\SearchInstructorCollection;
use Modules\Drivisa\Transformers\SearchInstructorTransformer;
use Modules\User\Repositories\UserRepository;

class InstructorController extends ApiBaseController
{
    /**
     * @var InstructorRepository
     */
    private $instructor;

    /**
     * @var InstructorService
     */
    private $instructorService;

    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(
        InstructorRepository $instructor,
        InstructorService    $instructorService,
        UserRepository       $user
    ) {
        $this->instructor = $instructor;
        $this->instructorService = $instructorService;
        $this->user = $user;
    }

    public function index(Request $request)
    {
        try {
            $instructors = $this->instructor->serverPaginationFilteringFor($request);
            return InstructorProfileTransformer::collection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Instructor updating his/her profile
     * @param UpdateInstructorProfileRequest $request
     * @return JsonResponse|InstructorProfileTransformer
     */
    public function updateInstructorProfile(UpdateInstructorProfileRequest $request)
    {
        DB::beginTransaction();
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
            $this->user->update($user, [
                'first_name' => $request->get('first_name', $user->first_name),
                'last_name' => $request->get('last_name', $user->last_name),
                'address' => $request->get('address', $user->address),
                'phone_number' => $request->get('phone_number', $user->phone_number),
                'city' => $request->get('city', $user->city),
                'birth_date' => $request->get('birth_date', $user->birth_date),
                'bio' => $request->get('bio', $user->bio),
                'postal_code' => $request->get('postal_code', $user->postal_code),
                'province' => $request->get('province', $user->province),
            ]);

            $user->unit_no = $request->get('unit_no', $user->unit_no);
            $user->street = $request->get('street', $user->street);
            $user->save();

            $this->instructor->update($instructor, $request->all());

            $instructor->di_number = $request->get('di_number', $instructor->di_number);
            $instructor->di_end_date = $request->get('di_end_date', $instructor->di_end_date);
            $instructor->licence_number = $request->get('licence_number', $instructor->licence_number);
            $instructor->licence_end_date = $request->get('licence_end_date', $instructor->licence_end_date);
            $instructor->save();

            DB::commit();
            return new InstructorProfileTransformer($instructor);
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get instructor by username
     * @param $username
     * @return JsonResponse|InstructorProfileTransformer
     */
    public function findByUsername($username)
    {
        try {
            return new InstructorProfileAppTransformer($this->instructorService->findByUsername($username));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function uploadSingleDocuments(UploadSingleDocumentRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->instructorService->uploadSingleDocument($authUser, $request->all());

            event(new DocumentUploadedEvent($authUser, $instructor));

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

            $instructor = $this->instructor->findByAttributes(['user_id' => $authUser->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->instructorService->uploadDocuments($authUser, $request->all());

            event(new DocumentUploadedEvent($authUser, $instructor));

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
        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
        if (!$instructor) {
            $message = trans('drivisa::drivisa.messages.instructor_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        return DocumentTransformer::collection($instructor->files);
    }

    /**
     * Search instructors
     * @param SearchInstructorsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchInstructors(SearchInstructorsRequest $request)
    {
        try {
            $instructors = $this->instructorService->searchInstructors($request->all());
            if($request->type == 'web') {
                return new SearchInstructorCollection($instructors);
            }
            return new SearchInstructorAppCollection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get authenticated instructor
     * @param Request $request
     * @return JsonResponse|InstructorProfileTransformer
     */
    public function me(Request $request)
    {
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
        return new InstructorProfileTransformer($instructor);
    }

    /**
     * Get authenticated instructor
     * @param Request $request
     * @return InstructorInfoTransformer
     */
    public function getProfileInfo(Request $request)
    {
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
        $instructorInfo = $this->instructorService->getProfileInfo($instructor);
        return new InstructorInfoTransformer($instructorInfo);
    }

    public function getTopInstructors(Request $request)
    {
        try {
            $instructors = $this->instructorService->getTopInstructors();
            return SearchInstructorTransformer::collection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function verifyInstructor(Request $request, Instructor $instructor)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor->verificationRequest()->create([
                'status' => VerificationRequest::STATUS['verified'],
                'verified_by' => $user->id,
                'verified_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $instructor->update(['verified' => 1]);

            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.instructor_verified'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function currentLesson(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);

            $current_ongoing_lesson = $this->instructorService->getCurrentOngoingLesson($instructor);

            if ($current_ongoing_lesson == null) {
                return $this->errorMessage("No Current Ongoing Lesson", 404);
            }

            return new InstructorCurrentLessonTransformer($current_ongoing_lesson);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
