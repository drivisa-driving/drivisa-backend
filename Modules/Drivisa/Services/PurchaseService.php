<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\TransactionRepository;

class PurchaseService
{
    private PurchaseRepository $purchaseRepository;

    /**
     * @param PurchaseRepository $purchaseRepository
     */
    public function __construct(
        PurchaseRepository $purchaseRepository
    )
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    public function create($data)
    {
        return $this->purchaseRepository->create([
            'purchaseable_id' => $data['id'],
            'purchaseable_type' => $data['type'],
            'transaction_id' => $data['transaction_id'],
            'type' => Purchase::TYPE[$data['type']],
            'trainee_id' => $data['trainee_id']
        ]);
    }

    public function getHistory($trainee)
    {
        return $trainee->purchases;
    }
}