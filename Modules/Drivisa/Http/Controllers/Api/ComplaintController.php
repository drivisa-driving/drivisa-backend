<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Complaint;
use Modules\Drivisa\Services\ComplaintService;
use Modules\Drivisa\Transformers\ComplaintTransformer;
use Modules\Drivisa\Repositories\ComplaintRepository;
use Modules\User\Repositories\UserRepository;

class ComplaintController extends ApiBaseController
{
    /**
     * @var ComplaintService
     */
    private $complaintService;

    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var ComplaintRepository
     */
    private $complaintRepository;

    public function __construct(
        ComplaintService $complaintService,
        UserRepository $user,
        ComplaintRepository $complaintRepository
    ) {
        $this->complaintService = $complaintService;
        $this->user = $user;
        $this->complaintRepository = $complaintRepository;
    }


    public function getComplaints(Request $request)
    {

        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        try {
            $complaints = $this->complaintRepository->serverPaginationFilteringFor($request);
            return ComplaintTransformer::collection($complaints);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function getComplaintByUser(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $authUser = $this->complaintRepository->findByAttributes(['user_id' => $user->id]);
            if (!$authUser) {
                $message = trans('drivisa::drivisa.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return ComplaintTransformer::collection($authUser->user->complaint);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function addComplaint(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->complaintService->addComplaint($user, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.complaint_raised'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMessage(trans('drivisa::drivisa.messages.can\'t_raise_your_complaint'));
        }
    }
}
