<?php

namespace Modules\Drivisa\Transformers\admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\User\Transformers\UserTransformer;

class TraineeAdminTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'fullName' => $this->resource->user->present()->fullname(),
            'verified' => $this->resource->user->isVerified(),
            'verified_at' => $this->verified_at ? Carbon::parse($this->verified_at)->format("M d Y h:i A") : "NA",
            'phoneNumber' => $this->resource->user->phone_number ?? "-",
            'email' => $this->resource->user->email,
            'user' => new UserTransformer($this->resource->user),
            'documents' => DocumentTransformer::collection($this->resource->files),
            'bio' => $this->resource->bio,
            'licenceType' => $this->resource->licence_type,
            'birthDate' => $this->resource->birth_date,
            'licenceStartDate' => $this->resource->licence_start_date,
            'licenceEndDate' => $this->resource->licence_end_date,
            'languages' => $this->resource->languages,
            'username' => $this->resource->user->username,
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'address' => $this->resource->user->address,
            'city' => $this->resource->user->city,
            'postalCode' => $this->resource->user->postal_code,
            'province' => $this->resource->user->province,
            'createdAt' => $this->resource->user->created_at->format("M d Y h:i A"),
            'hearFrom' => $this->resource->user->from_hear
        ];
    }
}
