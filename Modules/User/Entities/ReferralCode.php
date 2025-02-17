<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\Sentinel\User;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'sent_at',
        'used_at',
        'used_user_id'
    ];

    protected static function newFactory()
    {
        return \Modules\User\Database\factories\ReferralCodeFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
