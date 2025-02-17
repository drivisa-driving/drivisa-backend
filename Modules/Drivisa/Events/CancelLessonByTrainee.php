<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class CancelLessonByTrainee
{
    use SerializesModels;

    public $lesson;
    public $cancellationFee;
    public $refundAmount;
    public $instructor_refund_amount;

    public function __construct($lesson, $cancellationFee = null, $refundAmount = null, $instructor_refund_amount = null)
    {
        $this->lesson = $lesson;
        $this->cancellationFee = $cancellationFee;
        $this->refundAmount = $refundAmount;
        $this->instructor_refund_amount = $instructor_refund_amount;
    }
}
