<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'username' => $this->resource->username,
            'fullName' => $this->resource->present()->fullname,
            'avatar' => $this->resource->present()->gravatar(),
            'cover' => $this->resource->present()->cover(),
            'email' => $this->resource->email,
            'referral_code' => $this->resource->activeReferralCode,
            'createdAt' => $this->resource->created_at
        ];
    }
}
