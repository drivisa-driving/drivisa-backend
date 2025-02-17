<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeBankAccount extends Model
{
    use HasFactory, SoftDeletes;

    public const account_type = [
        'individual' => 'individual',
        'company' => 'company',
    ];
    protected $table = 'drivisa__stripe_bank_accounts';
    protected $fillable = [
        'country',
        'currency',
        'routing_number',
        'account_number',
        'account_holder_name',
        'account_holder_type',
    ];

}
