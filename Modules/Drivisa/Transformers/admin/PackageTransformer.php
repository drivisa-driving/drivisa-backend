<?php

namespace Modules\Drivisa\Transformers\admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageTransformer extends JsonResource
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
            'package_name_with_type' => $this->package_name_with_type,
            'packageType' => new PackageTypeTransformer($this->packageType),
            'packageData' => new PackageDataTransformer($this->packageData),
        ];
    }
}
