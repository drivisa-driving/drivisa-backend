<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Drivisa\Database\factories\RentalRequestFactory;

class RentalRequest extends Model
{
    use HasFactory;

    protected $table = 'drivisa__rental_requests';

    const STATUS = [
        'registered' => 1,
        'accepted' => 2,
        'paid' => 3,
        'rescheduled' => 4,
        'declined' => 5,
    ];

    const TYPE = [
        'g_test' => 1,
        'g2_test' => 2,
    ];

    protected $fillable = [
        'trainee_id',
        'package_id',
        'location',
        'latitude',
        'longitude',
        'booking_date',
        'booking_time',
        'status',
        'pickup_point',
        'dropoff_point',
        'purchase_id',
        'instructor_id',
        'type',
        'cancel_lesson_id',
        'expire_payment_time',
        'additional_tax',
        'additional_cost',
        'additional_km',
        'total_distance',
        'is_reschedule_request',
        'last_request_id',
        'lesson_id',
        'working_hour_id',
        'reschedule_payment_intent_id'
    ];

    protected $dates = [
        'expire_payment_time',
        'booking_date',
        'booking_time'
    ];

    public function instructors()
    {
        return $this->belongsToMany(InstructorRentalRequest::class, 'drivisa__instructor_rental_request')
            ->withPivot('instructor_id')
            ->using(InstructorRentalRequest::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id', 'id');
    }

    public function purchases()
    {
        return $this->morphMany(Purchase::class, 'purchaseable');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    protected static function newFactory()
    {
        return RentalRequestFactory::new();
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
