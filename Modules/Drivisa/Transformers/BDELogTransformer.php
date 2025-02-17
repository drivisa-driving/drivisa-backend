<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BDELogTransformer extends JsonResource
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
            'trainee_id' => $this->trainee_id,
            'instructor_id' => $this->instructor_id,
            'lesson_id' => $this->lesson_id,
            'bde_number' => $this->bde_number,
            'di_number' => $this->di_number,
            'instructor_sign' => $this->instructor_sign,
            'trainee_sign' => $this->trainee_sign,
            'notes' => $this->notes
        ];
    }
}
