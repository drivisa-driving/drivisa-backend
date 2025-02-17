<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TraineeProfileAdminTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'bio' => $this->resource->bio,
            'licenceType' => $this->resource->licence_type,
            'birthDate' => $this->resource->birth_date,
            'licenceStartDate' => $this->resource->licence_start_date,
            'licenceEndDate' => $this->resource->licence_end_date,
            'languages' => $this->resource->languages,
            'username' => $this->resource->user->username,
            'fullName' => $this->resource->user->present()->fullname(),
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'address' => $this->resource->user->address,
            'phoneNumber' => $this->resource->user->phone_number,
            'city' => $this->resource->user->city,
            'postalCode' => $this->resource->user->postal_code,
            'province' => $this->resource->user->province,
            'email' => $this->resource->user->email,
            'createdAt' => $this->resource->user->created_at,
        ];
    }
}
