<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Entities\ComplaintReply;

class Complaint extends Model
{
    use SoftDeletes, MediaRelation;

    protected $table = 'drivisa__complaint';
    protected $fillable = [
        'user_id', 'incident_date', 'reason', 'incident_summary'
    ];

    protected $appends = ['is_replied'];

    protected $with = ['complaintReply'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaintReply()
    {
        return $this->hasMany(ComplaintReply::class);
    }

    public function getIsRepliedAttribute()
    {
        return $this->complaintReply->count() > 0;
    }
}
