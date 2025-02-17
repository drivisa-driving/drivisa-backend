<?php

namespace Modules\Drivisa\Transformers\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Drivisa\Transformers\V2\SearchInstructorTransformer;

class SearchInstructorCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => SearchInstructorTransformer::collection($this->collection),
        ];
    }
}