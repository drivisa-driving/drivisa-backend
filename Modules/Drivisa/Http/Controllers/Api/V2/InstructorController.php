<?php

namespace Modules\Drivisa\Http\Controllers\Api\V2;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Http\Requests\SearchInstructorsRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Services\InstructorService;
use Modules\Drivisa\Transformers\V2\SearchInstructorAppCollection;
use Modules\Drivisa\Transformers\V2\SearchInstructorCollection;
use Modules\Drivisa\Transformers\V2\InstructorProfileAppTransformer;
use Modules\User\Repositories\UserRepository;

class InstructorController extends ApiBaseController
{

    /**
     * @var InstructorService
     */
    private $instructorService;
    public function __construct(

        InstructorService    $instructorService,
    ) {
        $this->instructorService = $instructorService;
    }
    public function findByUsername($username)
    {
        try {
            return new InstructorProfileAppTransformer($this->instructorService->findByUsername($username));
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
    public function searchInstructors(SearchInstructorsRequest $request)
    {
        try {
            $instructors = $this->instructorService->searchInstructors($request->all());
            if($request->type == 'web') {
                return new SearchInstructorCollection($instructors);
            }
            return new SearchInstructorAppCollection($instructors);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}