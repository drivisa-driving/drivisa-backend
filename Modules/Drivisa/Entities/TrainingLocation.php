<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__training_locations';
    protected $fillable = [
        'source_address',
        'source_latitude',
        'source_longitude'
    ];
}
