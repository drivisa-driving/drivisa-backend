<?php

namespace Modules\Drivisa\Services;

use Exception;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Entities\Complaint;
use Modules\Drivisa\Repositories\ComplaintRepository;
use Modules\User\Entities\Sentinel\User;

class ComplaintService
{

    private ComplaintRepository $complaintRepository;

    /**
     * @param ComplaintRepository $complaintRepository
     */


    public function __construct(ComplaintRepository $complaintRepository)
    {
        $this->complaintRepository = $complaintRepository;
    }

    public function getComplaint($id)
    {
        return $id;
    }
    
    public function addComplaint($user, $data)
    {
        return $this->complaintRepository->create([
            'user_id' => $user->id,
            'incident_date' => $data['incident_date'],
            'reason' => $data['reason'],
            'incident_summary' => $data['incident_summary'],
        ]);
    }
}
