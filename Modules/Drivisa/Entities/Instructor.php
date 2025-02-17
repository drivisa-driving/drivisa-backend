<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\InstructorFactory;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\User\Entities\Sentinel\User;

class Instructor extends Model
{
    use SoftDeletes, MediaRelation, HasFactory;

    protected $table = 'drivisa__instructors';
    protected $fillable = [
        'no',
        'first_name',
        'last_name',
        'bio',
        'verified',
        'verified_at',
        'user_id',
        'birth_date',
        'languages',
        'kyc_verification',
        'promotion_level',
        'signed_agreement',
        'licence_number',
        'licence_end_date',
        'di_number',
        'di_end_date',
        'signed_at'
    ];

    const KYC = [
        'Pending' => 1,
        'InProgress' => 2,
        'Approved' => 3,
        'Rejected' => 4
    ];

    protected $appends = ['full_name', 'stripe_account_id'];

    public function setLanguagesAttribute($value)
    {
        $this->attributes['languages'] = json_encode($value);
    }

    public static function newFactory()
    {
        return InstructorFactory::new();
    }

    public function getLanguagesAttribute($value)
    {
        return json_decode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function activePoint()
    {
        return $this->hasOne(Point::class)->where('is_active', 1);
    }

    public function workingDays()
    {
        return $this->hasMany(WorkingDay::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function verificationRequest()
    {
        return $this->morphOne(VerificationRequest::class, 'requestable');
    }

    public function stripeAccount()
    {

        return $this->hasOne(StripeAccount::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', 1);
    }

    public function rentalRequests()
    {
        return $this->belongsToMany(RentalRequest::class, 'drivisa__rental_requests')
            ->using(InstructorRentalRequest::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function bdeLog()
    {
        return $this->hasMany(BDELog::class);
    }

    public function finalTestResult()
    {
        return $this->hasMany(FinalTestResult::class);
    }

    public function completedLessonHours($hours)
    {
        $lessons = $this->lessons()->whereNotNull('ended_at')->get();

        return $lessons->sum('duration') >= $hours;
    }

    public function getStripeAccountIDAttribute()
    {
        return $this->stripeAccount()->pluck('account_id')->first();
    }

    public function instructorEarnings()
    {
        return $this->hasMany(InstructorEarning::class);
    }
}
