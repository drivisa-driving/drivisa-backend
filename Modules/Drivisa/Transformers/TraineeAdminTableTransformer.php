<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\User\Transformers\UserTransformer;

class TraineeAdminTableTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'fullName' => $this->resource->user->present()->fullname(),
            'verified' => $this->resource->user->isVerified(),
            'verified_at' => $this->verified_at ? Carbon::parse($this->verified_at)->format("M d Y h:i A") : "NA",
            'phoneNumber' => $this->resource->user->phone_number ?? "-",
            'email' => $this->resource->user->email,
            'user' => new UserTransformer($this->resource->user),
            'documents' => DocumentTransformer::collection($this->resource->files),
            'createdAt' => $this->resource->user->created_at->format("M d Y h:i A")
        ];
    }
}
