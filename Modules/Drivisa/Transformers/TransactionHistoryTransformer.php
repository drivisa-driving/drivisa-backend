<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'amount' => $this->amount,
            'tx_id' => $this->tx_id,
            'payment_intent_id' => $this->payment_intent_id,
            'charge_id' => $this->charge_id,
            'date' => Carbon::parse($this->created_at)->format("d-M-Y"),
        ];
    }
}
