<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;

class FinalTestLog extends Model
{

    protected $table = 'drivisa__final_test_logs';
    protected $fillable = [
        'final_test_result_id',
        'final_test_key_id',
        'mark_first',
        'mark_second',
        'mark_third',
    ];

    public function finalTestResult()
    {
        return $this->belongsTo(FinalTestResult::class);
    }

    public function finalTestKey()
    {
        return $this->belongsTo(FinalTestKey::class);
    }
}
