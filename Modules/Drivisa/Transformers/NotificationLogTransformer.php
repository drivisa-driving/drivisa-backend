<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Trainee;

class NotificationLogTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $instructor =Instructor::find($this->instructor_id);
        $trainee =Trainee::find($this->trainee_id);
        return [
            'id' => $this->id,
            'activity_name'=>$this->activity_name,
            'status'=>$this->status,
            'message'=>$this->message,
            'device_id'=>$this->device_id,
            'data'=>$this->data,
            'instructor'=>$instructor?->first_name.' '.$instructor?->last_name,
            'trainee'=>$trainee?->first_name.' '.$trainee?->last_name,
            'instructor_id'=>$this->instructor_id,
            'trainee_id'=>$this->trainee_id,
            'created_at'=>$this->created_at,
        ];
    }
}
