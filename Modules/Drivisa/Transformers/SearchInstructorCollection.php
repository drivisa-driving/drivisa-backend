<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchInstructorCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => SearchInstructorTransformer::collection($this->collection),
        ];
    }
}