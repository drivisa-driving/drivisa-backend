<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarkingKeyLog extends Model
{

    protected $table = 'drivisa__marking_keys_logs';

    const MARK = [
        'E' => 1,
        'M' => 2,
        'P' => 3,
        'N' => 4,
    ];

    protected $fillable = [
        'bde_log_id',
        'marking_key_id',
        'mark'
    ];

    public function markingKey()
    {
        return $this->belongsTo(MarkingKey::class);
    }

    public function bdeLog()
    {
        return $this->belongsTo(BDELog::class);
    }
}
