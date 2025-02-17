<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountUserTransformer extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user'=>$this->type=='driving'?$this->user?->first_name.' '.$this->user?->last_name:$this->trainee?->user?->first_name.' '.$this->trainee?->user?->last_name,
            'discount_id'=>$this->discount_id,
            'code'=>$this->allDiscount?->code,
            'discount_amount'=>$this->discount_amount,
            'discount_type'=>strtoupper($this->discount_type),
            'main_amount'=>$this->main_amount,
            'total_discount'=>$this->total_discount,
            'after_discount'=>$this->after_discount,
            'discount_used_name'=>str_replace('_',' ',ucwords($this->discount_used_name)) ,
            'created_at'=>Carbon::parse($this->created_at)->format('D, F d, Y h:i a'),
            'updated_at'=>$this->updated_at,
            'type'=>$this->type,
            'type_id'=>$this->type_id,
        ];

    }
}