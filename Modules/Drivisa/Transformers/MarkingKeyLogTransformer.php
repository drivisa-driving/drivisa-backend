<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkingKeyLogTransformer extends JsonResource
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
            'bde_log_id' => $this->bde_log_id,
            'marking_key_id' => $this->marking_key_id,
            'mark' => $this->mark,
        ];
    }
}
