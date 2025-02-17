<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Entities\Complaint;

class ComplaintReply extends Model
{
    use MediaRelation;

    protected $table = 'drivisa__complaint_reply';
    protected $fillable = [
        'complaint_id', 'reply'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
