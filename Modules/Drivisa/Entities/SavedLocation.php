<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Entities\Sentinel\User;

class SavedLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivisa__saved_locations';

    protected $fillable = [
        'source_latitude',
        'source_longitude',
        'source_address',
        'destination_latitude',
        'destination_longitude',
        'destination_address',
        'default',
        'trainee_id',
        'user_id'
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
