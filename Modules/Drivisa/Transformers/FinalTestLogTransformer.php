<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinalTestLogTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'final_test_result_id' => $this->final_test_result_id,
            'final_test_key_id' => $this->final_test_key_id,
            'mark_first' => $this->mark_first,
            'mark_second' => $this->mark_second,
            'mark_third' => $this->mark_third,
        ];
    }
}
