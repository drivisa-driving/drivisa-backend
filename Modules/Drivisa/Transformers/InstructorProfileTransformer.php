<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\RentalRequest;

class InstructorProfileTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'bio' => $this->resource->bio,
            'signed_agreement' => $this->resource->signed_agreement,
            'car_rental_count' => $this->getCarRentalCount(),
            'birth_date' => $this->resource->birth_date,
            'licence_number' => $this->resource->licence_number,
            'licence_end_date' => $this->resource->licence_end_date,
            'di_number' => $this->resource->di_number,
            'di_end_date' => $this->resource->di_end_date,
            'languages' => $this->resource->languages,
            'kycVerification' => array_search($this->resource->kyc_verification, Instructor::KYC),
            'evaluation' => [
                'avg' => $this->resource->lessons()->avg('trainee_evaluation->value'),
                'count' => $this->resource->lessons()->whereNotNull('trainee_evaluation')->count(),
                'comments' => InstructorCommentTransformer::collection($this->resource->lessons()->whereNotNull('trainee_note')->orderBy('updated_at', 'desc')->get())
            ],
            $this->mergeWhen($this->resource->user->exists(), function () {
                return [
                    'username' => $this->resource->user->username,
                    'fullName' => $this->resource->user->present()->fullname(),
                    'avatar' => $this->resource->user->present()->gravatar(),
                    'cover' => $this->resource->user->present()->carCover(),
                    'address' => $this->resource->user->address,
                    'phoneNumber' => $this->resource->user->phone_number,
                    'city' => $this->resource->user->city,
                    'postalCode' => $this->resource->user->postal_code,
                    'province' => $this->resource->user->province,
                    'street' => $this->resource->user->street,
                    'unit_no' => $this->resource->user->unit_no,
                    'email' => $this->resource->user->email,
                    'createdAt' => $this->resource->user->created_at
                ];
            }),
            'lessons' => [
                'count' => $this->lessons()->where('status', Lesson::STATUS['completed'])->count(),
                'trainee' => $this->lessons()->where('status', Lesson::STATUS['completed'])->distinct()->count('trainee_id'),
                'hours' => $this->lessons->whereNotNull('ended_at')->sum(function ($item) {
                    $start_at = Carbon::parse($item->end_at);
                    $end_at = Carbon::parse($item->start_at);
                    $duration = $start_at->diffInHours($end_at);
                    return $duration;
                }),
            ],
            'cars' => CarTransformer::collection($this->resource->cars),
            'point' => PointTransformer::collection($this->points()->where('is_active', true)->get()),
        ];
    }

    public function getCarRentalCount()
    {
        $query = "select count(*) as total_request from drivisa__rental_requests drr
                    inner join drivisa__instructor_rental_request dirr
                    on dirr.rental_request_id = drr.id 
                    where dirr.instructor_id = ? and drr.status = 1 and date_add(created_at, interval +1 day) >= NOW()";

        $data = DB::select($query, [$this->resource->id]);

        return count($data) > 0 ? $data[0]->total_request : 0;
    }
}
