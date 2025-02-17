<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InstructorRentalRequest extends Pivot
{
    protected $table = 'drivisa__instructor_rental_request';

    protected $fillable =
        [
            'rental_request_id',
            'instructor_id'
        ];
}