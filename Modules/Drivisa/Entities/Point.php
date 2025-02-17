<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Database\factories\PointFactory;

class Point extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__points';
    protected $fillable = [
        'source_name',
        'destination_name',
        'source_latitude',
        'source_longitude',
        'destination_latitude',
        'destination_longitude',
        'is_active',
        'source_address',
        'destination_address',
    ];
    protected $guarded = [
        'instructor_id'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    protected static function newFactory()
    {
        return PointFactory::new();
    }

}
