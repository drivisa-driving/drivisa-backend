<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\Account;
use Stripe\Stripe;

class StripeAccountTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->routing_number) {
            [$transit_number, $institution_number] = explode('-', $this->routing_number);
        }
        return [
            'id' => $this->id,
            'accountId' => $this->account_id,
            'status' => '',
            'stripe_account_details' => $this->getStripeAccountDetails(),
            'account_number' => $this->account_number,
            'account_holder_name' => $this->account_holder_name,
            'account_holder_type' => $this->account_holder_type,
            'transit_number' => isset($transit_number) ? $transit_number : null,
            'institution_number' => isset($institution_number) ? $institution_number : null,
            'createdAt' => $this->created_at,
        ];
    }

    private function getStripeAccountDetails()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        return Account::retrieve($this->account_id);
    }
}
