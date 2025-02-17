<?php

namespace Modules\Drivisa\Services;



use App\NotificationLog;
use Illuminate\Support\Facades\Log;

class LogService
{

    public function createNotificationLog($data,$message,$player_id,$string='',$activity_name=''){
      $instructor_id =$data->instructor_id;
       $trainee_id =$data->trainee_id;
        if(is_array($player_id)) {
            foreach ($player_id as $player) {
                $array = array('activity_name' => $activity_name,
                    'status' => '',
                    'device_id' => $player,
                    'message' =>$message ,
                    'data' => $string,
                    'trainee_id' => $trainee_id,
                    'instructor_id' => $instructor_id);
                 NotificationLog::create($array);
            }
        }else{
            $array = array('activity_name' => 'Notification',
                'status' => '',
                'device_id' => '-',
                'message' =>$message ,
                'data' => $string,
                'trainee_id' => $trainee_id,
                'instructor_id' => $instructor_id);
            NotificationLog::create($array);
        }
    }
    public function createLog($message,$player_id,$string='',$activity_name=''){
        if(is_array($player_id)) {
            foreach ($player_id as $player) {
                $array = array('activity_name' => $activity_name,
                    'status' => '',
                    'device_id' => $player,
                    'message' =>$message ,
                    'data' => $string,
                    'trainee_id' => 0,
                    'instructor_id' => 0);
                NotificationLog::create($array);
            }
        }else{
            $array = array('activity_name' => 'Notification',
                'status' => '',
                'device_id' => '-',
                'message' =>$message ,
                'data' => $string,
                'trainee_id' => 0,
                'instructor_id' => 0);
            NotificationLog::create($array);
        }
    }
}