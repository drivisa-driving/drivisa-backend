<?php

namespace Modules\User\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTransformer extends JsonResource
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
            'message' => isset($this->data['message']) ? $this->data['message'] : null,
            'avatar' => isset($this->data['avatar']) ? $this->data['avatar'] : null,
            'is_read' => $this->read_at != null,
            'read_at' => $this->read_at != null ? Carbon::parse($this->read_at)->format('d-m-Y h:i A') : null,
            'notification_at' => Carbon::parse($this->created_at)->format('d-m-Y h:i A')
        ];
    }
}
