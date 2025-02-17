<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingLocationTransformer extends JsonResource
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
            'sourceAddress' => $this->source_address,
            'sourceLatitude' => $this->source_latitude,
            'sourceLongitude' => $this->source_longitude,
            'createdAt' => $this->created_at,
        ];
    }
}
