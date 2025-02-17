<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\PackageFactory;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "drivisa__packages";

    protected $fillable = [
        'name',
        'package_type_id',
    ];

    protected $appends = ['package_name_with_type'];

    //relationships

    public function packageType()
    {
        return $this->belongsTo(PackageType::class);
    }

    public function packageData()
    {
        return $this->hasOne(PackageData::class);
    }

    public function purchases()
    {
        return $this->morphMany(Purchase::class, 'purchaseable');
    }

    public function getPackageNameWithTypeAttribute()
    {
        return $this->name . ' - ' . $this->packageType->name;
    }

    protected static function newFactory()
    {
        return PackageFactory::new();
    }
}
