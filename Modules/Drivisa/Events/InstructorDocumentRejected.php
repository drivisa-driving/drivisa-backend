<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class InstructorDocumentRejected
{
    use SerializesModels;

    public $instructor;
    public $document_name;
    public $reason;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $document_name, $reason)
    {
        $this->instructor = $user;
        $this->document_name = $document_name;
        $this->reason = $reason;
    }
}
