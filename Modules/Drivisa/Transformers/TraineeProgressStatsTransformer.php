<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TraineeProgressStatsTransformer extends JsonResource
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
            'question' => $this['title'],
            'total_point' => $this['points'],
            'value' => (int)$this['value'],
            'percentage' => $this->getPercentage((int)$this['points'], (int)$this['value'])
        ];
    }

    private function getPercentage($baseValue, $gotValue)
    {
        if($baseValue == 0){
            return 100;
        }
        return ($gotValue / $baseValue) * 100;
    }
}
