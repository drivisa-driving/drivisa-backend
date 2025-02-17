<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\StripeAccountFactory;

class StripeAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__stripe_accounts';
    protected $fillable = [
        'account_id',
        'instructor_id',
        'account_holder_name',
        'account_holder_type',
        'account_number',
        'country',
        'currency',
        'fingerprint',
        'last4',
        'routing_number',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    protected static function newFactory()
    {
        return StripeAccountFactory::new();
    }
}
