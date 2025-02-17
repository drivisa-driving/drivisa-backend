<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable =[
        'title',
        'code',
        'discount_amount',
        'status',
        'type',
        'start_at',
        'expire_at',
        'package_ids'
    ];
    protected $casts = [
        'discount_amount' => 'float',
    ];
}
