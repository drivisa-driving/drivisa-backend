<?php

namespace App\Jobs;

use App\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationLogCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data,$message,$player_id,$string,$activity_name,$type,$trainee_id,$instructor_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type,$data,$message,$player_id,$string='',$activity_name='',$trainee_id=0,$instructor_id=0)
    {

        $this->type =$type;
        $this->data =$data;
        $this->message =$message;
        $this->player_id =$player_id;
        $this->string=$string;
        $this->activity_name=$activity_name;
        $this->trainee_id=$trainee_id;
        $this->instructor_id=$instructor_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type==1) {
            $instructor_id = $this->data?->instructor_id??0;
            $trainee_id = $this->data?->trainee_id??0;
            if (is_array($this->player_id)) {
                foreach ($this->player_id as $player) {
                    $array = array('activity_name' => $this->activity_name,
                        'status' => '',
                        'device_id' => $player,
                        'message' => $this->message,
                        'data' => $this->string,
                        'trainee_id' => $trainee_id,
                        'instructor_id' => $instructor_id);
                    NotificationLog::create($array);
                }
            } else {
                $array = array('activity_name' => $this->activity_name ?? 'Notification',
                    'status' => '',
                    'device_id' => '-',
                    'message' => $this->message,
                    'data' => $this->string,
                    'trainee_id' => $trainee_id,
                    'instructor_id' => $instructor_id);
                NotificationLog::create($array);
            }
        }else if ($this->type==2){
            if(is_array($this->player_id)) {
                foreach ($this->player_id as $player) {
                    $array = array('activity_name' => $this->activity_name,
                        'status' => '',
                        'device_id' => $player,
                        'message' =>$this->message ,
                        'data' => $this->string,
                        'trainee_id' => $this->trainee_id,
                        'instructor_id' => $this->instructor_id);
                    NotificationLog::create($array);
                }
            }else{
                $array = array('activity_name' =>$this->activity_name ?? 'Notification',
                    'status' => '',
                    'device_id' => '-',
                    'message' =>$this->message ,
                    'data' => $this->string,
                    'trainee_id' => $this->trainee_id,
                    'instructor_id' => $this->instructor_id);
                NotificationLog::create($array);
            }
        }
    }
}
