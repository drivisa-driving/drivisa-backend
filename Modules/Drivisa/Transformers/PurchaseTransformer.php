<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Purchase;

class PurchaseTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => ucwords(array_search($this->type, Purchase::TYPE)),
            'transaction' => $this->transaction,
            'lesson' => $this->lesson,
            'date' => Carbon::parse($this->created_at)->format('M d, Y h:i A'),
        ];
    }
}
