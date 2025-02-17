<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\FinalTestLog;
use Modules\Drivisa\Entities\MarkingKeyLog;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeBDELogTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        $instructorCollection = Instructor::find(BDELogTransformer::collection($this->bdeLog)->pluck('instructor_id'));


        $instructors = [];

        foreach ($instructorCollection as $instructor) {
            $instructors['full_name'] = $instructor->first_name . " " . $instructor->last_name;
            $instructors['di_number'] = $instructor->di_number;
        }

        return [
            'trainee' => [
                'trainee_id' => $this->id,
                'full_name' => $this->first_name . " " . $this->last_name,
                'address' => $this->user->address,
                'city' => $this->user->city,
                'postal_code' => $this->user->postal_code,
                'phone_number' => $this->user->phone_number,
                'licence_start_date' => $this->licence_start_date,
                'licence_end_date' => $this->licence_end_date,
            ],
            'instructors' => $instructors,
            'bde_log' =>  BDELogTransformer::collection($this->bdeLog),
            'marking_log' => MarkingKeyLogTransformer::collection(MarkingKeyLog::all()),
            'final_log' => FinalTestLogTransformer::collection(FinalTestLog::all()),
        ];
    }
}
