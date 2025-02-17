<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class NewBuyPackage
{
    use SerializesModels;

    public $trainee;
    public $package;
    public $totalChargeable;
    public $discountPrice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($trainee, $package, $totalChargeable = null, $discountPrice = null)
    {
        $this->trainee = $trainee;
        $this->package = $package;
        $this->totalChargeable = $totalChargeable;
        $this->discountPrice = $discountPrice;
    }
}
