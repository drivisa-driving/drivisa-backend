<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__courses';
    protected $fillable = [
        'package_id',
        'user_id',
        'status',
        'credit',
        'payment_intent_id',
        'transaction_id',
        'charge_id',
        'type',
        'is_licence_issued',
        'previous_course_id',
        'is_extra_credit_hours'
    ];

    const STATUS = [
        'initiated' => 1,
        'progress' => 2,
        'completed' => 3,
        'canceled' => 4,
    ];

    const TYPE = [
        'BDE' => 1,
        'Package' => 2,
        'Refund' => 3,
        'Refund_BDE' => 4,
        'Cancelled_Credits' => 6,
        'Bonus' => 7,
        'Bonus_BDE' => 8
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function creditUseHistories()
    {
        return $this->hasMany(CreditUseHistory::class);
    }

    public function purchase()
    {
        return $this->morphOne(Purchase::class, 'purchaseable');
    }

    public static function newFactory()
    {
        return CourseFactory::new();
    }
}
