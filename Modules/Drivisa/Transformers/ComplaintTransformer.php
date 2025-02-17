<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Drivisa\Entities\Complaint;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Entities\ComplaintReply;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->User['first_name'] . " " . $this->User['last_name'],
            'complaint_date' => $this->created_at,
            'incident_date' => Carbon::parse($this->incident_date)->format('m/d/Y'),
            'reason' => $this->reason,
            'incident_summary' => $this->incident_summary,
            'replies' => ComplaintReplyTransformer::collection($this->complaintReply),
            'is_replied' => $this->is_replied ? "Replied" : "Not Replied",
        ];
    }
}
