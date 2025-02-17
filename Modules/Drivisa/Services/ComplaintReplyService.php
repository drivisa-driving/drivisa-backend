<?php

namespace Modules\Drivisa\Services;

use Exception;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Entities\ComplaintReply;
use Modules\Drivisa\Repositories\ComplaintReplyRepository;
use Modules\User\Entities\Sentinel\User;

class ComplaintReplyService
{

    private ComplaintReplyRepository $complaintReplyRepository;

    /**
     * @param ComplaintReplyRepository $complaintReplyRepository
     */


    public function __construct(ComplaintReplyRepository $complaintReplyRepository)
    {
        $this->complaintReplyRepository = $complaintReplyRepository;
    }
    
    public function addComplaintReply($complaint_id, $user, $data)
    {
        return $this->complaintReplyRepository->create([
            'complaint_id' => $complaint_id->id,
            'reply' => $data['reply'],
        ]);
    }
}
