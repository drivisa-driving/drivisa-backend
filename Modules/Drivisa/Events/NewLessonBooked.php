<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class NewLessonBooked
{
    use SerializesModels;

    public $trainee;
    public $instructor;
    public $transaction;
    public $purchase;
    public $lesson;
    public $is_credit;
    public $cost;
    public $tax;
    public $additional_charge;
    public $additional_tax;
    public $extra_distance;
    public $commission;
    public $totalCost;
    public $totalDiscount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($trainee,
                                $instructor,
                                $transaction = null,
                                $purchase = null,
                                $lesson = null,
                                $is_credit = null,
                                $cost = null,
                                $tax = null,
                                $additional_charge = null,
                                $additional_tax = null,
                                $extra_distance = null,
                                $commission = null,
                                $totalCost = null,
                                $totalDiscount=0)
    {
        $this->trainee = $trainee;
        $this->instructor = $instructor;
        $this->transaction = $transaction;
        $this->purchase = $purchase;
        $this->lesson = $lesson;
        $this->is_credit = $is_credit;
        $this->cost = $cost;
        $this->tax = $tax;
        $this->additional_charge = $additional_charge;
        $this->additional_tax = $additional_tax;
        $this->extra_distance = $extra_distance;
        $this->commission = $commission;
        $this->totalCost = $totalCost;
        $this->totalDiscount = $totalDiscount;
    }
}
