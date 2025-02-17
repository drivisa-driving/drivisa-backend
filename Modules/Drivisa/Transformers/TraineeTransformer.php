<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\PackageType;
use Modules\Drivisa\Entities\FinalTestResult;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeTransformer extends JsonResource
{
    public function toArray($request)
    {
        $bdePrice = $this->getBDEPricing();

        return [
            'id' => $this->resource->id,
            'no' => $this->resource->no,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'bio' => $this->resource->bio,
            'credit_remaining' => $this->resource->user->credit,
            'total_credit_remaining' => $this->resource->user->total_credit,
            'licenceType' => $this->resource->licence_type,
            'birthDate' => $this->resource->birth_date,
            'licenceStartDate' => $this->resource->licence_start_date,
            'licenceEndDate' => $this->resource->licence_end_date,
            'kycVerification' => array_search($this->resource->kyc_verification, Trainee::KYC),
            $this->mergeWhen($this->resource->user->exists(), function () {
                return [
                    'username' => $this->resource->user->username,
                    'fullName' => $this->resource->user->present()->fullname(),
                    'avatar' => $this->resource->user->present()->gravatar(),
                    'cover' => $this->resource->user->present()->cover(),
                    'address' => $this->resource->user->address,
                    'phoneNumber' => $this->resource->user->phone_number,
                    'city' => $this->resource->user->city,
                    'postalCode' => $this->resource->user->postal_code,
                    'province' => $this->resource->user->province,
                    'street' => $this->resource->user->street,
                    'unit_no' => $this->resource->user->unit_no,
                    'email' => $this->resource->user->email,
                    'createdAt' => $this->resource->user->created_at,
                    'createdAtFormatted' => Carbon::parse($this->resource->user->created_at)->format("M d Y h:i A"),
                    'verified_at' => $this->verified_at ? Carbon::parse($this->verified_at)->format("M d Y h:i A") : "NA",
                ];
            }),
            'isFinalTestCompleted' => $this->isFinalTestCompleted(),
            'bde_discount_price' => $bdePrice[0],
            'bde_amount' => $bdePrice[1],
            'enable_reset_pick_drop' => 1
        ];
    }

    private function isFinalTestCompleted()
    {
        $bdeLogs = $this->bdeLog;
        if ($bdeLogs) {
            $finalTestLog = FinalTestResult::whereIn('bde_log_id', $bdeLogs->pluck('id')->toArray())->first();
        }

        return $finalTestLog ? 1 : 0;
    }

    private function getBDEPricing()
    {
        $packageType = PackageType::where('name', 'BDE')->first();
        $packageData = $packageType?->packages()?->where('package_type_id', $packageType->id)?->first()?->packageData;

        return [$packageData?->discount_price, $packageData?->amount];
    }
}
