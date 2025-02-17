<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    use HasFactory;

    public const STATUS = [
        'unverified' => 1,
        'verified' => 2,
    ];

    protected $table = 'drivisa__verification_requests';
    protected $fillable = [
        'verified_by',
        'verified_at',
        'status',
        'message'
    ];

    public function verificationRequestable()
    {
        return $this->morphTo();
    }

}
