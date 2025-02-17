<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SavedLocationTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'source_latitude' => $this->source_latitude,
            'source_longitude' => $this->source_longitude,
            'source_address' => $this->source_address,
            'destination_latitude' => $this->destination_latitude,
            'destination_longitude' => $this->destination_longitude,
            'destination_address' => $this->destination_address,
            'default' => $this->default ? "Yes" : "No",
            'trainee' => $this->resource->trainee,
        ];
    }
}
