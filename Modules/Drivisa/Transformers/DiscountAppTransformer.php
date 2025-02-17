<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountAppTransformer extends JsonResource
{

    public function toArray($request)
    {
        return ['id' => $this->id,
            'title' => $this->title,
            'code'=>$this->code,
            'discount_amount'=>$this->discount_amount,
            'type'=>$this->type,
            'status'=>$this->status,
            'type_format'=>strtoupper($this->type),
            'status_format'=>strtoupper($this->status),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];

    }
}