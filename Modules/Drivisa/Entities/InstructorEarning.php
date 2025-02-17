<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorEarning extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'drivisa__instructor_earnings';

    protected $fillable = ['lesson_id', 'instructor_id', 'type', 'amount', 'additional_cost', 'tax', 'total_amount', 'created_at', 'updated_at'];


    const TYPE = [
        'lesson_complete' => 1,
        'reschedule_lesson' => 2,
        'cancel_lesson' => 3
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
