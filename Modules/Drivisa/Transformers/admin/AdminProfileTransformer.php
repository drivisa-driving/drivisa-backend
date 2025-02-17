<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProfileTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'fullName' => $this->resource->present()->fullname(),
            'avatar' => $this->resource->present()->gravatar(),
            'cover' => $this->resource->present()->cover(),
            'address' => $this->resource->address,
            'phoneNumber' => $this->resource->phone_number,
            'city' => $this->resource->city,
            'postalCode' => $this->resource->postal_code,
            'province' => $this->resource->province,
            'email' => $this->resource->email,
            'createdAt' => $this->resource->created_at,
        ];
    }
}