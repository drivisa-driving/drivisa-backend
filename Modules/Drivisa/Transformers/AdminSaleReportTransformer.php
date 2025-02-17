<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Purchase;

class AdminSaleReportTransformer extends JsonResource
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
            'type' => ucwords(str_replace('_', ' ', array_search($this->type, Purchase::TYPE))),
            'amount' => '$' . $this->transaction->amount,
            'trainee_id' => $this->trainee->id,
            'trainee_name' => $this->trainee->first_name . " " . $this->trainee->last_name,
            'date' => Carbon::parse($this->created_at)->format('D, M d, Y h:i A'),
        ];
    }
}
