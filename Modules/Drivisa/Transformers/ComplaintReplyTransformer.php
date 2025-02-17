<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintReplyTransformer extends JsonResource
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
            'complaint_id' => $this->complaint_id,
            'reply' => $this->reply,
        ];
    }
}
