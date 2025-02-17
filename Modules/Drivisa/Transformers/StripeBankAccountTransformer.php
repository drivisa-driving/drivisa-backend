<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StripeBankAccountTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $transitNumber = substr($this->routing_number, 0, 5);
        $institutionNumber = substr($this->routing_number, 5);

        return [
            'id' => $this->id,
            'country' => $this->country,
            'currency' => $this->currency,
            'transitNumber' => $transitNumber,
            'institutionNumber' => $institutionNumber,
            'accountNumber' => $this->account_number,
            'accountHolderName' => $this->account_holder_name,
            'accountHolderType' => $this->account_holder_type,
            'createdAt' => $this->created_at,
        ];
    }
}
