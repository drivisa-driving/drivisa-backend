<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Transaction;

class TransactionTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $transaction = Transaction::where('tx_id', $this->id)->with('purchase')->first();

        $lesson = null;

        if ($transaction) {
            $lesson = Lesson::find($transaction->purchase->lesson_id);
        }

        return [
            'id' => $this->id,
            'net' => abs($this->net) / 100,
            'type' => $this->type,
            'availableOn' => gmdate("Y-m-d\ H:i:s", $this->available_on),
            'created' => gmdate("Y-m-d\ H:i:s", $this->created),
            'day' => Carbon::parse($this->created)->englishDayOfWeek,
            'status' => $this->status,
            'lesson' => new LessonInstructorTransformer($lesson)
        ];
    }
}
