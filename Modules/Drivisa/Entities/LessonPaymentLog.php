<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonPaymentLog extends Model
{
    use HasFactory;

    protected $table = "drivisa__lesson_payment_logs";

    protected $fillable = [
        'lesson_id',
        'charge_type',
        'transaction_id',
        'payment_intent_id',
        'charge_id',
        'amount'
    ];

    const CHARGE_TYPE = [
        'reset_pick_drop' => 1,
        'reschedule_lesson' => 2,
        'cancel_lesson' => 3
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
