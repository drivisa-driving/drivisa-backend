<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Facades\Modules\Core\Services\Formatters\CurrencyFormatter;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Transformers\admin\PackageTransformer;

class DiscountTransformer extends JsonResource
{

    public function toArray($request)
    {
        return ['id' => $this->id,
            'title' => $this->title,
            'code'=>$this->code,
            'discount_amount'=>$this->discount_amount,
            'discount_amount_formatted'=>'$'.number_format($this->discount_amount,2),
            'type'=>$this->type,
            'package_ids'=>array_map('intval',explode(',',$this->package_ids)),
            'packages'=> implode(',',PackageTransformer::collection(Package::whereIn('id',array_map('intval',explode(',',$this->package_ids)))->get())->pluck('package_name_with_type')->toArray()),
            'status'=>$this->status,
            'type_format'=>strtoupper($this->type),
            'status_format'=>strtoupper($this->status),
            'created_at'=>Carbon::parse($this->created_at)->format('D, F d, Y h:i a'),
            'updated_at'=>$this->updated_at,
            'start_at'=>Carbon::parse($this->start_at)->format('Y-m-d'),
            'expire_at'=>Carbon::parse($this->expire_at)->format('Y-m-d'),
            'start_at_formatted'=>Carbon::parse($this->start_at)->format('D, F d, Y h:i a'),
            'expire_at_formatted'=>Carbon::parse($this->expire_at)->format('D, F d, Y h:i a'),
        ];

    }
}