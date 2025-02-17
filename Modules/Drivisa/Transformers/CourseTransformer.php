<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\User\Transformers\UserTransformer;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Services\StatsService;

class CourseTransformer extends JsonResource
{

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->statsService = app(StatsService::class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            ['name' => 'package', 'data' => $this->statsService->getStatsByType($this, "Package", true)],
            ['name' => 'bde', 'data' => $this->statsService->getStatsByType($this, "BDE", true)],
        ];
    }
}
