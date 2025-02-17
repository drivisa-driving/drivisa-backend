<?php

namespace Modules\Drivisa\Transformers\admin;

use Carbon\Carbon;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Transformers\UserTransformer;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\Drivisa\Transformers\InstructorCommentTransformer;

class InstructorAdminTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'username' => $this->resource->user->username,
            'birthDate' => $this->resource->birth_date,
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'address' => $this->resource->user->address,
            'city' => $this->resource->user->city,
            'province' => $this->resource->user->province,
            'postalCode' => $this->resource->user->postal_code,
            'languages' => $this->resource->languages,
            'fullName' => $this->resource->user->present()->fullname(),
            'licenceNumber' => $this->resource->licence_number,
            'licenceEndDate' => $this->resource->licence_end_date,
            'diNumber' => $this->resource->di_number,
            'diEndDate' => $this->resource->di_end_date,
            'signDocument' => $this->resource->signed_agreement,
            'signed_at' => $this->signed_at ? Carbon::parse($this->signed_at)->format("M d Y h:i A") : "NA",
            'verified' => $this->resource->user->isVerified(),
            'verified_at' => $this->verified_at ? Carbon::parse($this->verified_at)->format("M d Y h:i A") : "NA",
            'phoneNumber' => $this->resource->user->phone_number ?? "-",
            'email' => $this->resource->user->email,
            'user' => new UserTransformer($this->resource->user),
            'documents' => DocumentTransformer::collection($this->resource->files),
            'lessons' => [
                'count' => $this->lessons()->count(),
                'trainee' => $this->lessons()->distinct()->count('trainee_id'),
                'hours' => $this->lessons->whereNotNull('ended_at')->sum(function ($item) {
                    $start_at = Carbon::parse($item->end_at);
                    $end_at = Carbon::parse($item->start_at);
                    $duration = $start_at->diffInHours($end_at);
                    return $duration;
                }),
            ],
            'evaluation' => [
                'avg' => number_format((float)$this->resource->lessons()->avg('trainee_evaluation->value'), 1, '.', ''),
                'count' => $this->resource->lessons()->whereNotNull('trainee_evaluation')->count(),
                'comments' => InstructorCommentTransformer::collection($this->resource->lessons()->whereNotNull('trainee_note')->orderBy('updated_at', 'desc')->get())
            ],
            'createdAt' => $this->resource->user->created_at->format("M d Y h:i A"),
            'stripe_account_id' => $this->stripe_account_id ?? null,
            'hearFrom' => $this->resource->user->from_hear
        ];
    }
}
