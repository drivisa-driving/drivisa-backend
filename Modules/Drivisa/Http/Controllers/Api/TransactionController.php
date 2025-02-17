<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Repositories\TransactionRepository;
use Modules\Drivisa\Services\TransactionService;
use Modules\Drivisa\Transformers\CourseTransformer;
use Modules\Drivisa\Transformers\TransactionHistoryTransformer;
use Modules\Drivisa\Transformers\TransactionTransformer;

class TransactionController extends ApiBaseController
{
    private TransactionRepository $transactionRepository;
    private TransactionService $transactionService;
    private TraineeRepository $traineeRepository;

    /**
     * @param TransactionRepository $transactionRepository
     * @param TransactionService $transactionService
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionService    $transactionService,
        TraineeRepository     $traineeRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->transactionService = $transactionService;
        $this->traineeRepository = $traineeRepository;
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

            $transactions = $this->transactionService->getTransactions($trainee);

            return TransactionHistoryTransformer::collection($transactions);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
