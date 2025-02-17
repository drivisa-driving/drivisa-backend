<?php

namespace Modules\Pusher\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    const MESSAGE_BY = [
        'trainee' => 1,
        'instructor' => 2,
        'admin' => 3
    ];

    const RULES = [
        'lesson_id' => 'required',
        'message' => 'required',
        'message_by' => 'required'
    ];

    protected $fillable = [
        'trainee_id',
        'instructor_id',
        'lesson_id',
        'message',
        'message_by',
        'request_id'
    ];

}
