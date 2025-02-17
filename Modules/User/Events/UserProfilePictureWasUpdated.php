<?php

namespace Modules\User\Events;

use Illuminate\Database\Eloquent\Model;
use Modules\Media\Contracts\StoringMedia;
use Modules\User\Entities\Sentinel\User;

class UserProfilePictureWasUpdated implements StoringMedia
{
    /**
     * @var array
     */
    public $data;
    /**
     * @var User
     */
    public $user;

    public function __construct($user, array $data)
    {
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * Return the entity
     * @return Model
     */
    public function getEntity()
    {
        return $this->user;
    }

    /**
     * Return the ALL data sent
     * @return array
     */
    public function getSubmissionData()
    {
        return $this->data;
    }
}
