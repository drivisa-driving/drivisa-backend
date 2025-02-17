<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Services\PurchaseService;
use Modules\Drivisa\Transformers\CourseTransformer;
use Modules\Drivisa\Transformers\PurchaseTransformer;

class PurchaseController extends ApiBaseController
{

    /**
     * @param TraineeRepository $traineeRepository
     * @param PurchaseRepository $purchaseRepository
     * @param PurchaseService $purchaseService
     */
    public function __construct(
        private TraineeRepository  $traineeRepository,
        private PurchaseRepository $purchaseRepository,
        private PurchaseService    $purchaseService
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $trainee = $this->traineeRepository->findByAttributes(['user_id' => $user->id]);
            if (!$trainee) {
                $message = trans('drivisa::drivisa.messages.trainee_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $purchases = $this->purchaseService->getHistory($trainee);

            return PurchaseTransformer::collection($purchases);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
