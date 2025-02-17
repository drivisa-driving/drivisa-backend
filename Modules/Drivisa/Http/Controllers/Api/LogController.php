<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\NotificationLog;
use Modules\Drivisa\Transformers\NotificationLogTransformer;

class LogController extends Controller
{
    public function getNotificationLogs(){
       return response(['data'=>NotificationLogTransformer::collection(NotificationLog::orderBy('id','desc')->paginate()),'total'=>NotificationLog::count()]) ;
    }
}
