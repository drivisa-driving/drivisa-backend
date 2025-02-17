<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Trainee;
use Modules\User\Entities\Sentinel\User;

class PublicUserTransformer extends JsonResource
{
    public static $wrap = 'user';

    public function toArray($request)
    {
        $data = [
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'username' => $this->resource->username,
            'fullName' => $this->resource->present()->fullname,
            'avatar' => $this->resource->present()->gravatar(),
            'cover' => $this->resource->present()->cover(),
            'email' => $this->resource->email,
            'credit' => $this->resource->credit,
            'total_credit' => $this->resource->total_credit,
            'total_pending_road_test' => $this->resource->pending_road_test_payment_count,
            'latest_road_test_id' => $this->resource->latest_road_test_id,
            'createdAt' => $this->resource->created_at,
            'activated' => $this->resource->isActivated(),
            'token' => new TokenTransformer($this->resource->getFirstApiKey()),
            'player_id' => $this->resource->player_id,
            'roles' => FullRoleTransformer::collection($this->resource->roles),
            'userType' => $this->resource->user_type,
            'verified' => $this->resource->isVerified(),
            'model' => User::class,
            'referral_code' => $this->resource->activeReferralCode?->code,
            'total_unread_notifications' => count($this->resource->unreadNotifications)
        ];

        if ($this->resource->user_type == 1) {
            if ($this->resource->instructor) {
                $data['kycVerification'] = array_search($this->resource->instructor->kyc_verification, Instructor::KYC);
            }
        } else if ($this->resource->user_type == 2) {
            if ($this->resource->trainee) {
                $data['kycVerification'] = array_search($this->resource->trainee->kyc_verification, Trainee::KYC);
            }
        }


        return $data;
    }
}
