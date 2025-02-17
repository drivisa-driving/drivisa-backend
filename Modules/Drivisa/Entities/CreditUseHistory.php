<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Drivisa\Database\factories\CreditUseHistoryFactory;

class CreditUseHistory extends Model
{
    use HasFactory;

    protected $table = 'drivisa__credit_use_histories';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'used_at',
        'credit_used',
        'note'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public static function newFactory()
    {
        return CreditUseHistoryFactory::new();
    }
}
