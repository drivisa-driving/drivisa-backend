<?php

namespace Modules\Drivisa\Transformers\admin;

use Carbon\Carbon;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Transformers\UserTransformer;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\Drivisa\Transformers\InstructorCommentTransformer;

class InstructorAdminTableTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'username' => $this->resource->user->username,
            'province' => $this->resource->user->province,
            'postalCode' => $this->resource->user->postal_code,
            'fullName' => $this->resource->user->present()->fullname(),
            'diEndDate' => $this->resource->di_end_date,
            'signDocument' => $this->resource->signed_agreement,
            'signed_at' => $this->signed_at ? Carbon::parse($this->signed_at)->format("M d Y h:i A") : "NA",
            'verified' => $this->resource->user->isVerified(),
            'verified_at' => $this->verified_at ? Carbon::parse($this->verified_at)->format("M d Y h:i A") : "NA",
            'phoneNumber' => $this->resource->user->phone_number ?? "-",
            'email' => $this->resource->user->email,
            'user' => new UserTransformer($this->resource->user),
            'documents' => DocumentTransformer::collection($this->resource->files),
            'createdAt' => $this->resource->user->created_at->format("M d Y h:i A"),
        ];
    }
}
