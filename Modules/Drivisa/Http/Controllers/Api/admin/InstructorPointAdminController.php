<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Transformers\PointTransformer;

class InstructorPointAdminController extends ApiBaseController
{


    public function __construct()
    {
    }

    public function index(Request $request, Instructor $instructor)
    {
        try {
            $points = $instructor->points;
            return PointTransformer::collection($points);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

}