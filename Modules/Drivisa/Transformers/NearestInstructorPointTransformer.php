<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NearestInstructorPointTransformer extends JsonResource
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
            'sourceName' => $this->source_name,
            'sourceAddress' => $this->source_address,
            'destinationName' => $this->destination_name,
            'destinationAddress' => $this->destination_address,
            'sourceLatitude' => $this->source_latitude,
            'sourceLongitude' => $this->source_longitude,
            'destinationLatitude' => $this->destination_latitude,
            'destinationLongitude' => $this->destination_longitude,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
            'instructor' => new InstructorTransformer($this->instructor),
        ];
    }
}
