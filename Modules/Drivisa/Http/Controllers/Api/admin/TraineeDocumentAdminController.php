<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Transformers\DocumentTransformer;

class TraineeDocumentAdminController extends ApiBaseController
{


    public function __construct()
    {
    }

    public function index(Request $request, Trainee $trainee)
    {
        try {
            $files = $trainee->files;
            return DocumentTransformer::collection($files);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}