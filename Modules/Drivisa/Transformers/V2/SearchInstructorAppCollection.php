<?php

namespace Modules\Drivisa\Transformers\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Drivisa\Transformers\V2\SearchInstructorAppTransformer;

class SearchInstructorAppCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'data' => SearchInstructorAppTransformer::collection($this->collection),
        ];
    }
}