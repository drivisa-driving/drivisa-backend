<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class CancelLessonByInstructor
{
    use SerializesModels;

    public $lesson;
    public $refundAmount;
    public $additional_costs;
    public $cancellationFee;

    public function __construct($lesson, $refundAmount = null, $additional_costs = null, $cancellationFee = null)
    {
        $this->lesson = $lesson;
        $this->refundAmount = $refundAmount;
        $this->additional_costs = $additional_costs;
        $this->cancellationFee = $cancellationFee;
    }
}
