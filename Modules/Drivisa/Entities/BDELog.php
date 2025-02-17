<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;

class BDELog extends Model
{
    protected $table = 'drivisa__bde_logs';
    protected $fillable = [
        'trainee_id',
        'instructor_id',
        'lesson_id',
        'bde_number',
        'di_number',
        'instructor_sign',
        'trainee_sign',
        'notes'
    ];

    const RULES = [
        'trainee_id' => 'required',
        'instructor_id' => 'required',
        'lesson_id' => 'required',
        'instructor_sign' => 'required',
        'trainee_sign' => 'required',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function lessons()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function markingKeyLog()
    {
        return $this->hasMany(MarkingKeyLog::class, 'bde_log_id', 'id');
    }

    public function finalTestResult()
    {
        return $this->hasMany(FinalTestResult::class, 'bde_log_id', 'id');
    }
}
