<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinalTestResult extends Model
{
    protected $table = 'drivisa__final_test_results';
    protected $fillable = [
        'bde_log_id',
        'instructor_id',
        'instructor_sign',
        'final_marks',
        'is_pass',
        'di_number'
    ];

    public function bdeLog()
    {
        return $this->belongsTo(BDELog::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function finalTestLog()
    {
        return $this->hasMany(FinalTestLog::class);
    }
}
