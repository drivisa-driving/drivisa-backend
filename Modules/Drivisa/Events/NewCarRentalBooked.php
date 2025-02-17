<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class NewCarRentalBooked
{
    use SerializesModels;

    public $rentalRequest;
    public $packageData;
    public $tax;
    public $totalCost;
    public $discountAmount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($rentalRequest, $packageData, $tax = null, $totalCost = null,$discountAmount=0)
    {
        $this->rentalRequest = $rentalRequest;
        $this->packageData = $packageData;
        $this->tax = $tax;
        $this->totalCost = $totalCost;
        $this->discountAmount = $discountAmount;
    }
}
