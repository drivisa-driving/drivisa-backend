<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchInstructorAppCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'data' => SearchInstructorAppTransformer::collection($this->collection),
        ];
    }
}