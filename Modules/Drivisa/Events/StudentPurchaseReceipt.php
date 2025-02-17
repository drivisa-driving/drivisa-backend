<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class StudentPurchaseReceipt
{
    use SerializesModels;

    public $user;
    public $document_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $document_name)
    {
        $this->user = $user;
        $this->document_name = $document_name;
    }
}
