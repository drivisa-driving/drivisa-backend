<?php

namespace Modules\Pusher\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Pusher\Entities\Message;

class MessageTransformer extends JsonResource
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
            'message_by' => array_search($this->message_by, Message::MESSAGE_BY),
            'text' => $this->message,
            'timestamp' => Carbon::parse($this->created_at)->format('m-d-Y h:i A'),
        ];
    }
}
