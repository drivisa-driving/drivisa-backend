<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Repositories\TransactionRepository;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\Instructor;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private PurchaseRepository $purchaseRepository;
    private LessonRepository $lessonRepository;
    private LessonCancellationRepository $lessonCancellationRepository;

    /**
     * @param TransactionRepository $transactionRepository
     * @param PurchaseRepository $purchaseRepository
     * @param LessonRepository $lessonRepository
     * @param lessonCancellationRepository $lessonCancellationRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        PurchaseRepository    $purchaseRepository,
        LessonRepository $lessonRepository,
        LessonCancellationRepository $lessonCancellationRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->lessonRepository = $lessonRepository;
        $this->lessonCancellationRepository = $lessonCancellationRepository;
    }

    public function create($data)
    {
        return $this->transactionRepository->create([
            'amount' => $data['amount'],
            'payment_intent_id' => $data['payment_intent_id'],
            'tx_id' => $data['tx_id'],
            'charge_id' => $data['charge_id'],
            'response' => json_encode($data['response']),
        ]);
    }

    public function getTransactions($trainee)
    {

        $transactions = $trainee->purchases->pluck('transaction_id')->toArray();

        return $this->transactionRepository->whereIn('id', $transactions)->get();
    }

    public function getInstructorTransaction(Instructor $instructor)
    {
        return $instructor->lessons->load(['purchases.transaction'])->whereNotNull('transaction_id');
    }

    public function getTraineeTransaction(Trainee $trainee)
    {
        return $trainee->lessons->load(['purchases.transaction'])->whereNotNull('transaction_id');
    }

    public function getTraineeRefunds($trainee)
    {
        $cancelled_lessons = $this->lessonRepository->where('status', 4)->where('trainee_id', $trainee->id)->get();

        $lessonCancellation = collect();

        foreach ($cancelled_lessons as $cancelled_lesson) {
            $cancelled = $this->lessonCancellationRepository->where('lesson_id', $cancelled_lesson->id)->get();

            $lessonCancellation->push(...$cancelled);
        }

        return $lessonCancellation;
    }
}
