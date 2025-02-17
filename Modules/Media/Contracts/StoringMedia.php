<?php

namespace Modules\Media\Contracts;

use Illuminate\Database\Eloquent\Model;

interface StoringMedia
{
    /**
     * Return the entity
     * @return Model
     */
    public function getEntity();

    /**
     * Return the ALL data sent
     * @return array
     */
    public function getSubmissionData();
}
