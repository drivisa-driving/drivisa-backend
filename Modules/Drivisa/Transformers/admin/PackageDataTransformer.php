<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Facades\Settings;

class PackageDataTransformer extends JsonResource
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
            'hours' => $this->hours,
            'hour_charge' => $this->hour_charge,
            'amount' => $this->amount,
            'additional_information' => $this->additional_information,
            'instructor' => $this->instructor ?: "0",
            'drivisa' => $this->drivisa ?: "0",
            'pdio' => $this->pdio ?: "0",
            'mto' => $this->mto ?: "0",
            'instructor_cancel_fee' => $this->instructor_cancel_fee ?: "0",
            'pdio_cancel_fee' => $this->pdio_cancel_fee ?: "0",
            'mto_cancel_fee' => $this->mto_cancel_fee ?: "0",
            'drivisa_cancel_fee' => $this->drivisa_cancel_fee ?: "0",
            'discount_price' => $this->discount_price ?: "0",
            'tax' => ($this->discount_price * (Settings::get('lesson_tax') / 100)) ?: "0",
            'total' => $this->discount_price + ($this->discount_price * (Settings::get('lesson_tax') / 100)),
        ];
    }
}
