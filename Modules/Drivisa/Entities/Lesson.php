<?php

namespace Modules\Drivisa\Entities;

use Carbon\Carbon;
use Modules\Setting\Facades\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stripe;

class Lesson extends Model
{
    use SoftDeletes, HasFactory;

    public const STATUS = [
        'reserved' => 1,
        'inProgress' => 2,
        'completed' => 3,
        'canceled' => 4,
        'rescheduled' => 5,
    ];

    const HST_PERCENTAGE = 13;

    const TYPE = [
        'driving' => 1,
        'bde' => 2,
        'car_rental' => 3,
        'g_test' => 4,
        'g2_test' => 5,
    ];

    const PAYMENT_BY = [
        'stripe' => 1,
        'credit' => 2,
    ];

    const STATIC_TAX_VALUE = 0.13; # conversion of 13/100

    const CHARGE = [
        1 => 60,
        2 => 115
    ];

    const INSTRUCTOR_FEES = [
        1 => 40,
        2 => 80
    ];

    const INSTRUCTOR_DRIVING_CANCEL_FEES = [
        1 => 20,
        2 => 40
    ];

    const INSTRUCTOR_ROAD_TEST_CANCEL_FEES = [
        1 => 50,
        2 => 50
    ];

    const HST_IMPLEMENT_DATE = '2022-08-16 04:31:32';


    protected $table = 'drivisa__lessons';
    protected $fillable = [
        'no', 'start_at', 'start_time', 'lesson_type',
        'end_at', 'started_at',
        'ended_at', 'is_request',
        'confirmed', 'cost',
        'commission', 'net_amount',
        'tax', 'paid_at',
        'pickup_point', 'dropoff_point',
        'instructor_note', 'instructor_evaluation',
        'trainee_note', 'trainee_evaluation',
        'instructor_id', 'trainee_id',
        'created_by', 'status', 'transaction_id',
        'payment_intent_id', 'charge_id',
        'working_hour_id',
        'transfer_id',
        'payment_by',
        'additional_tax',
        'additional_cost',
        'additional_km',
        'bde_number',
        'rental_request_id',
        'instructor_notification_id',
        'trainee_notification_id',
        'is_rescheduled',
        'rescheduled_payment_id',
        'notification_sent_at',
        'is_notification_sent',
        'times_rescheduled',
        'reschedule_time',
        'lesson_end_notification_sent_at',
        'lesson_end_notification_count',
        'credit_use_histories_id',
        'is_bonus_credit',
        'is_refund_initiated',
        'last_lesson_id',
        'reset_pick_drop'
    ];
    protected $attributes = [
        'status' => Lesson::STATUS['reserved'],
    ];

    protected $appends = ['purchase_amount', 'duration'];

    public static function newFactory()
    {
        return LessonFactory::new();
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function lessonCancellation()
    {
        return $this->hasOne(LessonCancellation::class, 'lesson_id', 'id');
    }

    public function purchases()
    {
        return $this->morphMany(Purchase::class, 'purchaseable');
    }

    public function getPurchaseAmountAttribute()
    {
        $discount =  DiscountUser::where('type', array_search($this->lesson_type, Lesson::TYPE))
            ->where('user_id', $this->trainee_id)->where('type_id', $this->id)->first();
      
        if ($discount) {
            return ($this->cost - $discount->total_discount) + $this->tax + $this->additional_cost + $this->additional_tax;
        }
        return $this->cost + $this->tax + $this->additional_cost + $this->additional_tax;
    }

    public function getDurationAttribute()
    {
        $start_at = Carbon::parse($this->start_at);
        $end_at = Carbon::parse($this->end_at);
        $duration = $start_at->diffInMinutes($end_at) / 60;

        if ($duration == 2.5) return $duration;

        return $duration > 2 ? $duration == 22 ? 2 : 1 : $duration;
    }

    public function bdeLog()
    {
        return $this->hasMany(BDELog::class);
    }

    // scopes

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS['completed']);
    }

    public function isNotificationSent()
    {
        return $this->notification_sent_at != null;
    }

    public function lessonUnder30Minutes()
    {
        $now = now();
        $lessonTime = Carbon::parse($this->start_at)->subMinutes(30);

        return $now->greaterThanOrEqualTo($lessonTime);
    }

    public function getInstructorEarning()
    {
        $earning = 0;
        if ($this->lesson_type == self::TYPE['driving']) {
            $earning = Settings::get('instructor_driving_fee') * $this->duration;
        }

        if ($this->lesson_type == self::TYPE['bde']) {
            $earning = Settings::get('instructor_bde_fee') * $this->duration;
        }

        return $earning;
    }

    public function getInstructorEarningForRoadTest()
    {
        $earning = 0;
        if ($this->lesson_type == self::TYPE['g_test']) {
            $earning = Settings::get('instructor_g_test_fee');
        }

        if ($this->lesson_type == self::TYPE['g2_test']) {
            $earning = Settings::get('instructor_g2_test_fee');
        }

        return $earning;
    }

    public function getHstAmount()
    {
        $earning = $this->getInstructorEarning();
        return $this->calculateHST($earning);
    }

    public function calculateHST($amount)
    {
        return ($amount * (self::HST_PERCENTAGE / 100));
    }

    public function getInstructorRescheduledEarning()
    {
        return $this->getCompensationEarning();
    }


    public function getInstructorCancelledEarning()
    {
        $earning = 0;
        if (!$this->lessonCancellation) return $earning;

        return $this->getCompensationEarning();
    }

    private function addHstToAmount($amount)
    {
        return $amount + ($amount * (self::HST_PERCENTAGE / 100));
    }

    /**
     * @param int $earning
     * @return int
     */
    private function getCompensationEarning(): int
    {
        $earning = 0;
        if ($this->lesson_type == self::TYPE['driving']) {
            $earning = self::INSTRUCTOR_DRIVING_CANCEL_FEES[$this->duration];
        }

        if ($this->lesson_type == self::TYPE['bde']) {
            $earning = self::INSTRUCTOR_DRIVING_CANCEL_FEES[$this->duration];
        }

        if ($this->lesson_type == Lesson::TYPE['g_test']) {
            $earning = self::INSTRUCTOR_ROAD_TEST_CANCEL_FEES[$this->duration];
        }

        if ($this->lesson_type == Lesson::TYPE['g2_test']) {
            $earning = self::INSTRUCTOR_ROAD_TEST_CANCEL_FEES[$this->duration];
        }
        return $earning;
    }

    public function lessonType()
    {
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson_type, Lesson::TYPE)));

        if ($lesson_type == 'Driving') {
            $lesson_type = "In Car Private Lesson";
        } else if ($lesson_type == 'Bde') {
            $lesson_type = "BDE";
        }

        return $lesson_type;
    }

    public function instructorEarning()
    {
        return $this->hasOne(InstructorEarning::class);
    }

    public function lessonPaymentLogs()
    {
        return $this->hasMany(LessonPaymentLog::class);
    }

    public function getOldPurchaseAmountAttribute()
    {
        $refundedAmount = $this->calculateRefundedAmount();
        $transactionAmount = $this->getTransactionAmount();
        $lessonPaymentLogsSum = $this->getLessonPaymentLogsSum();

        return max(0, (float)$transactionAmount - $refundedAmount) + $lessonPaymentLogsSum ?: "NA";
    }

    private function calculateRefundedAmount()
    {
        $refundedAmount = 0;

        if ($this->payment_intent_id) {

            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            $refunds = $stripe->refunds->all(['payment_intent' => $this->payment_intent_id]);

            if (!empty($refunds)) {
                foreach ($refunds as $refund) {
                    $refundedAmount += $refund->amount;
                }
            }
        }

        return $refundedAmount / 100;
    }

    private function getTransactionAmount()
    {
        $transaction = Transaction::where('tx_id', $this->transaction_id)->first();
        return $transaction ? $transaction->amount : 0;
    }

    private function getLessonPaymentLogsSum()
    {
        return $this->lessonPaymentLogs()->where('lesson_id', $this->id)->sum('amount');
    }
}
