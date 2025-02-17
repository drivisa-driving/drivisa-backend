<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\PackageDataFactory;

class PackageData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "drivisa__package_data";

    protected $fillable = [
        'name',
        'hours',
        'hour_charge',
        'amount',
        'additional_information',
        'package_id',
        'instructor',
        'drivisa',
        'pdio',
        'mto',
        'instructor_cancel_fee',
        'drivisa_cancel_fee',
        'pdio_cancel_fee',
        'mto_cancel_fee',
        'discount_price'
    ];

    protected static function newFactory()
    {
        return PackageDataFactory::new();
    }
}
