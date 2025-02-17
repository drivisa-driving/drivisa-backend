<?php

namespace Modules\Drivisa\Transformers\WebSchedules;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\DiscountUser;
use Modules\Drivisa\Entities\Lesson;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Services\BDEService;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Services\StatsService;
use Illuminate\Http\Resources\Json\JsonResource;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class LessonTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'no' => $this->no,
            'status' => $this->status,
            'status_text' => array_search($this->status, Lesson::STATUS),
        ];
    }

}
