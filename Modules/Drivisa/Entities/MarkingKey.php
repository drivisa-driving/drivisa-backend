<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;

class MarkingKey extends Model
{
    protected $table = 'drivisa__marking_keys';
    protected $fillable = ['title'];

    public function markingKeyLog()
    {
        return $this->hasMany(MarkingKeyLog::class);
    }
    
}
