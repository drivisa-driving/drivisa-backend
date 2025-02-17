<?php

namespace Modules\Drivisa\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Drivisa\Database\factories\WorkingDayFactory;

class WorkingDay extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS = [
        'available' => 1,
        'canceled' => 2,
    ];
    public const DAYS_OF_WEEK = [
        'Sunday' => 1,
        'Monday' => 2,
        'Tuesday' => 3,
        'Wednesday' => 4,
        'Thursday' => 5,
        'Friday' => 6,
        'Saturday' => 7,
    ];

    protected $table = 'drivisa__working_days';
    protected $fillable = [
        'status',
        'date',
        'instructor_id'
    ];

    public static function newFactory()
    {
        return WorkingDayFactory::new();
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function workingHoursWithActivePoint()
    {
        return $this->workingHours()
            ->where('status', WorkingHour::STATUS['available'])
            ->whereHas('point', function ($query) {
                $query->where('drivisa__points.is_active', true);
            })
            ->orderBy('open_at');
    }

    public function allWorkingHoursWithActivePoint()
    {
        return $this->workingHours()
            ->whereHas('point', function ($query) {
                $query->where('drivisa__points.is_active', true);
            })
            ->orderBy('open_at');
    }

    public function allWorkingHours()
    {
        return $this->workingHours()->orderBy('open_at');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
