<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorTransactionTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $transaction = Transaction::where('tx_id', $this->transaction_id)->first();
        $lesson_type = str_replace('_', " ", array_search($this->lesson_type, Lesson::TYPE));

        return [
            'id' => $this->id,
            'lesson_no' => $this->no,
            'lesson_type' => ucwords($lesson_type),
            'start_at' => Carbon::parse($this->start_at)->format('D, M d, Y h:i A'),
            'end_at' => Carbon::parse($this->end_at)->format('D, M d, Y h:i A'),
            'amount' => $transaction->amount,
            'payment_intent_id' => $this->payment_intent_id,
            'txn_id' => $this->transaction_id,
            'trainee' => $this->trainee,
            'instructor' => $this->instructor,
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}
