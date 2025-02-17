<?php

namespace Modules\Drivisa\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Drivisa\Database\factories\WorkingHourFactory;

class WorkingHour extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS = [
        'available' => 1,
        'unavailable' => 2,
    ];

    protected $table = 'drivisa__working_hours';
    protected $fillable = [
        'working_day_id',
        'status',
        'open_at',
        'close_at',
        'point_id',
    ];

    protected $appends = ['duration'];

    public static function newFactory()
    {
        return WorkingHourFactory::new();
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function workingDay()
    {
        return $this->belongsTo(WorkingDay::class);
    }

    public function getDurationAttribute()
    {
        $start_at = Carbon::parse($this->open_at);
        $end_at = Carbon::parse($this->close_at);

        return $start_at->diffInHours($end_at);
    }
}
