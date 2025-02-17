<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\LessonCancellation;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Repositories\TransactionRepository;
use Modules\Drivisa\Services\TransactionService;
use Modules\Drivisa\Transformers\admin\TransactionAdminTransformer;
use Modules\Drivisa\Transformers\InstructorTransactionTransformer;
use Modules\Drivisa\Transformers\RefundTransformer;
use Modules\Drivisa\Transformers\TraineeTransactionTransformer;

class TransactionAdminController extends ApiBaseController
{
    private TransactionRepository $transactionRepository;
    private LessonCancellationRepository $lessonCancellationRepository;
    private TransactionService $transactionService;

    /**
     * @param TransactionRepository $transactionRepository
     * @param LessonCancellationRepository $lessonCancellationRepository
     * @param TransactionService $transactionService
     */
    public function __construct(
        TransactionRepository        $transactionRepository,
        LessonCancellationRepository $lessonCancellationRepository,
        TransactionService           $transactionService
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->lessonCancellationRepository = $lessonCancellationRepository;
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        try {
         $transactions = $this->transactionRepository->serverPaginationFilteringFor($request);
         return TransactionAdminTransformer::collection($transactions);
        } catch (\Throwable $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function refund(Request $request)
    {
        try {
            $lessonCancellation = $this->lessonCancellationRepository->serverPaginationFilteringFor($request);
            return RefundTransformer::collection($lessonCancellation);
        } catch (\Throwable $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getInstructorTransactions(Request $request, Instructor $instructor)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->transactionService->getInstructorTransaction($instructor);

            return InstructorTransactionTransformer::collection($lessons);

        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }

    public function getTraineeTransactions(Request $request, Trainee $trainee)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $lessons = $this->transactionService->getTraineeTransaction($trainee);

            return TraineeTransactionTransformer::collection($lessons);

        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}
