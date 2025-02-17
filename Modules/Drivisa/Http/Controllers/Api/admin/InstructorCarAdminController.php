<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Transformers\CarTransformer;

class InstructorCarAdminController extends ApiBaseController
{


    public function __construct()
    {
    }

    public function index(Request $request, Instructor $instructor)
    {
        try {
            $cars = $instructor->cars;
            return CarTransformer::collection($cars);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

}