<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Trainee;

class NotificationLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'log_db';
    protected $table = 'notification_log';
    protected $fillable = [
        'activity_name',
        'status',
        'message',
        'instructor_id',
        'trainee_id',
        'device_id',
        'data'
    ];

}
