<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_user_id',
        'user_id',
        'base_amount',
        'tax',
        'amount',
        'data'
    ];

//    protected static function newFactory()
//    {
//        return \Modules\User\Database\factories\ReferralTransactionsFactory::new();
//    }
}
