<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
