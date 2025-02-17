<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Http\Requests\NearestInstructorsPointsRequest;
use Modules\Drivisa\Http\Requests\StoreInstructorPointRequest;
use Modules\Drivisa\Http\Requests\TogglePointRequest;
use Modules\Drivisa\Http\Requests\UpdateInstructorPointRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\InstructorPointService;
use Modules\Drivisa\Transformers\NearestInstructorPointTransformer;
use Modules\Drivisa\Transformers\PointTransformer;

class InstructorPointController extends ApiBaseController
{
    /**
     * @var InstructorPointService
     */
    protected $instructorPointService;

    private $instructor;

    public function __construct(InstructorPointService $instructorPointService,
                                InstructorRepository $instructor)
    {
        $this->instructor = $instructor;
        $this->instructorPointService = $instructorPointService;
    }

    /**
     * Get instructor's points
     * @param Request $request
     * @param Instructor $instructor
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request, Instructor $instructor)
    {
        try {
            $points = $this->instructorPointService->getPoints($instructor);
            return PointTransformer::collection($points);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete instructor's point
     * @param Request $request
     * @param Point $point
     * @return JsonResponse
     */
    public function destroy(Request $request, Point $point)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $this->instructorPointService->deletePoint($authUser, $point);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.point_deleted'));
        } catch (Exception $e) {
            if ($e instanceof AuthorizationException) {
                return $this->errorMessage(trans('user::auth.action_unauthorized'), Response::HTTP_FORBIDDEN);
            }
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update instructor's point
     * The request should contains source name,destination name,
     * point status(is_active),latitude and longitude to source,
     * latitude and longitude to destination,
     * @param UpdateInstructorPointRequest $request
     * @param Point $point
     * @return JsonResponse
     */
    public function update(UpdateInstructorPointRequest $request, Point $point)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            $points = $this->distanceUpdate($request, $authUser->id,$point->id);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            if($points){
                return $this->errorMessage('You can’t update this location due to 10 KM away policy');
            }
            $this->instructorPointService->updatePoint($authUser, $point, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.point_updated'));

        } catch (Exception $e) {
            if ($e instanceof AuthorizationException) {
                return $this->errorMessage(trans('user::auth.action_unauthorized'), Response::HTTP_FORBIDDEN);
            }
            return $this->errorMessage($e->getMessage(), $e->getCode());

        }
    }
    public function distanceUpdate($request, $id,$point_id) {

        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
        $points = DB::table('drivisa__points')->where('instructor_id', $instructor->id)->where('deleted_at',null)
            ->select('drivisa__points.*', DB::raw("6371 * acos(cos(radians(" . $request->source_latitude . "))
             * cos(radians(source_latitude)) 
             * cos(radians(source_longitude) - radians(" . $request->source_longitude. ")) 
             + sin(radians(" . $request->source_latitude . ")) 
             * sin(radians(source_latitude))) AS distance"))
            ->having('distance', '<', 10 ?? 50000)
            ->where('id','!=', $point_id)
            ->get();
        if(count($points)){
            return 1;
        }
        return 0;
    }
    /**
     * Set instructor's point
     * The request should contains source and destination name,
     * point status,latitude and longitude to source,
     * latitude and longitude to destination,
     * @param StoreInstructorPointRequest $request
     * @return JsonResponse
     */
    public function setPoint(StoreInstructorPointRequest $request)
    {
        DB::beginTransaction();
        $authUser = $this->getUserFromRequest($request);
        $points = $this->distance($request, $authUser->id);
        try {
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            if($points){
                return $this->errorMessage('You can’t add this location due to 10 KM away policy');
            }
            $this->instructorPointService->setPoint($authUser, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.point_created'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());

        }
    }
    public function distance($request, $id) {

        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);

        $points = DB::table('drivisa__points')->where('instructor_id', $instructor->id)->where('deleted_at',null)
            ->select('drivisa__points.*', DB::raw("6371 * acos(cos(radians(" . $request->source_latitude . "))
             * cos(radians(source_latitude))
             * cos(radians(source_longitude) - radians(" . $request->source_longitude. "))
             + sin(radians(" . $request->source_latitude . "))
             * sin(radians(source_latitude))) AS distance"))
            ->having('distance', '<', 10 ?? 50000)
            ->get();
        if(count($points)){
            return 1;
        }
        return 0;
    }
    public function getPoints(Request $request)
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
            $points = $this->instructorPointService->getPoints($instructor);
            return PointTransformer::collection($points);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getActivePoints(Request $request)
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
            $points = $this->instructorPointService->getActivePoints($instructor);
            return PointTransformer::collection($points);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getPoint(Request $request, Point $point)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $point = $this->instructorPointService->getPoint($user, $point);
            return new PointTransformer($point);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * toggle points's status
     * The request should contains point's id and status,
     * @param TogglePointRequest $request
     * @param Point $point
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function togglePoint(TogglePointRequest $request, Point $point)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                return $this->errorMessage(trans('user::users.messages.user_not_found'), Response::HTTP_NOT_FOUND);
            }
            $points = $this->instructorPointService->togglePoint($authUser, $point, $request->all());
            DB::commit();
            return PointTransformer::collection($points);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get nearest instructor's Points
     * @param NearestInstructorsPointsRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getNearestInstructorsPoints(NearestInstructorsPointsRequest $request)
    {
        try {
            $points = $this->instructorPointService->getNearestInstructors($request->all());
            return NearestInstructorPointTransformer::collection($points);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}