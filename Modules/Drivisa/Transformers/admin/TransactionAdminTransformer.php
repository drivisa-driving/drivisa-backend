<?php

namespace Modules\Drivisa\Transformers\admin;

use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionAdminTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $instructor = "";
        if ($this->purchase) {
            if ($this->purchase->type !== 2 && $this->purchase->type !== 3) {
                $instructor = Instructor::find($this->purchase->purchaseable->instructor_id);
            }
        }

        return [
            'id' => $this->id,
            'amount' => '$' . $this->amount,
            'payment_intent_id' => $this->payment_intent_id,
            'type' => ucwords(array_search($this->purchase?->type, Purchase::TYPE)),
            'txn_id' => $this->tx_id,
            'trainee' => $this->purchase?->trainee,
            'instructor' => $instructor,
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}
