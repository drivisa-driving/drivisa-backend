<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\ComplaintReply;
use Modules\Drivisa\Services\ComplaintReplyService;
use Modules\Drivisa\Transformers\ComplaintReplyTransformer;
use Modules\Drivisa\Repositories\ComplaintReplyRepository;
use Modules\Drivisa\Repositories\ComplaintRepository;
use Modules\User\Repositories\UserRepository;

class ComplaintReplyController extends ApiBaseController
{
    public function __construct(
        private ComplaintReplyService    $complaintReplyService,
        private UserRepository           $user,
        private ComplaintReplyRepository $complaintReplyRepository,
        private ComplaintRepository      $complaintRepository
    )
    {
    }

    public function addComplaintReply(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $complaint = $this->complaintRepository->find($request->id);

            $this->complaintReplyService->addComplaintReply($complaint, $user, $request->all());
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.complaint_replied'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage(trans('drivisa::drivisa.messages.can\'t_reply'));
        }
    }
}
