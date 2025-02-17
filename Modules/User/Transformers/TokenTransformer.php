<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenTransformer extends JsonResource
{
    public function toArray($request)
    {
        return [
            'token' => $this->access_token,
            'expiredAt' => (string)$this->expired_at,
            'created_at' => $this->created_at,
        ];
    }
}
