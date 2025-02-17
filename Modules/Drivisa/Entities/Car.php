<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;

class Car extends Model
{
    use SoftDeletes, MediaRelation;

    protected $table = 'drivisa__cars';
    protected $fillable = [
        'make', 'model', 'generation', 'trim'
    ];


    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
