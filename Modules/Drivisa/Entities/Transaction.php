<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'drivisa__transactions';

    protected $fillable = [
        'amount',
        'payment_intent_id',
        'tx_id',
        'charge_id',
        'response',
    ];

    protected $casts = [
        'response' => 'array'
    ];

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
