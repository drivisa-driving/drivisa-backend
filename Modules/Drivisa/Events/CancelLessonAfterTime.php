<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class CancelLessonAfterTime
{
    use SerializesModels;

    public $lesson;
    public $inCarInstructorFees;
    public $cancellationFee;
    public $refund;

    public function __construct(
        $lesson,
        $inCarInstructorFees = null,
        $cancellationFee = null,
        $refund = null
    ) {
        $this->lesson = $lesson;
        $this->inCarInstructorFees = $inCarInstructorFees;
        $this->cancellationFee = $cancellationFee;
        $this->refund = $refund;
    }
}
