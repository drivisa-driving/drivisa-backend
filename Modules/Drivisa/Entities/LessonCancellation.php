<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\LessonCancellationFactory;

class LessonCancellation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__lesson_cancellations';

    protected $fillable = [
        'lesson_id',
        'cancel_at',
        'cancel_by',
        'reason',
        'refund_id',
        'instructor_fee',
        'drivisa_fee',
        'pdio_fee',
        'mto_fee',
        'time_left',
        'refund_choice',
        'is_refunded',
        'cancellation_fee',
        'refund_amount'
    ];

    const REFUND_CHOICE = [
        'Credit' => 1,
        'Cash' => 2,
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    protected static function newFactory()
    {
        return LessonCancellationFactory::new();
    }
}
