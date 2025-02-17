<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\TraineeFactory;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\User\Entities\Sentinel\User;

class Trainee extends Model
{
    use HasFactory, SoftDeletes, MediaRelation, HasFactory;

    protected $table = 'drivisa__trainees';
    protected $fillable = [
        'no',
        'first_name',
        'last_name',
        'bio',
        'verified',
        'verified_at',
        'licence_type',
        'user_id',
        'birth_date',
        'licence_start_date',
        'licence_end_date',
        'kyc_verification',
        'stripe_customer_id',
        'licence_number',
        'licence_issued'
    ];

    const KYC = [
        'Pending' => 1,
        'InProgress' => 2,
        'Approved' => 3,
        'Rejected' => 4
    ];

    protected $appends = ['full_name'];

    public static function newFactory()
    {
        return TraineeFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function verificationRequest()
    {
        return $this->morphOne(VerificationRequest::class, 'requestable');
    }

    public function savedLocations()
    {
        return $this->hasMany(SavedLocation::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', 1);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'trainee_id', 'id');
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function bdeLog()
    {
        return $this->hasMany(BDELog::class);
    }
}
