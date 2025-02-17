<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageTypeTransformer extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'instructor' => $this->instructor,
            'drivisa' => $this->drivisa,
            'pdio' => $this->pdio,
            'mto' => $this->mto,
            'instructor_cancel_fee' => $this->instructor_cancel_fee,
            'pdio_cancel_fee' => $this->pdio_cancel_fee,
            'mto_cancel_fee' => $this->mto_cancel_fee,
            'drivisa_cancel_fee' => $this->drivisa_cancel_fee,
            'packages_count' => $this->packages_count,
        ];
    }
}
