<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\PackageDataFactory;
use Modules\Drivisa\Database\factories\PackageTypeFactory;

class PackageType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "drivisa__package_types";

    protected $fillable = [
        'name',
        'instructor',
        'drivisa',
        'pdio',
        'mto',
        'instructor_cancel_fee',
        'drivisa_cancel_fee',
        'pdio_cancel_fee',
        'mto_cancel_fee'
    ];


    //relationships

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    protected static function newFactory()
    {
        return PackageTypeFactory::new();
    }

    public function count()
    {
        return PackageType::packages->count();
    }
}
