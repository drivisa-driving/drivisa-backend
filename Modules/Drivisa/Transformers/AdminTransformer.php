<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTransformer extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'fullName' => $this->resource->first_name . " " . $this->resource->last_name,
            'email' => $this->resource->email,
            'lastLogin' => $this->resource->last_login
                ? Carbon::parse($this->resource->last_login)->format("D, M d, Y h:i A")
                : "NA",
            'createdAt' => $this->resource->created_at->format("D, M d, Y h:i A")
        ];
    }
}
