<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class EarningTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $balance = $this->balance ?? null;
        $transactions = $this->transactions ?? null;
        $pending = $this->getBalance($balance, 'pending');
        $available = $this->getCurrentSessionBalance($transactions) - $this->getBalance($balance, 'pending');
        $balance = $this->getCurrentSessionBalance($transactions);

        return [
            'pending' => CurrencyFormatter::getFormattedPrice($pending),
            'available' => CurrencyFormatter::getFormattedPrice($available),
            'balance' => CurrencyFormatter::getFormattedPrice($balance),
            'transactions' => $transactions ? TransactionTransformer::collection($transactions) : [],
            'current_collection' => $transactions ? $this->currentCollection($transactions) : []
        ];
    }

    private function getBalance($transactions, $type = "available")
    {
        $balance = 0;
        if ($transactions[$type]) {
            foreach ($transactions[$type] as $value) {
                $balance += abs($value->amount);
            }
        }
        return $balance / 100;
    }

    private function getCurrentSessionBalance($transactions)
    {
        $balance = 0;
        if ($transactions) {
            foreach ($transactions as $key => $value) {
                $balance += abs($value->net);
            }
        }
        return $balance / 100;
    }

    private function currentCollection($transactions)
    {
        $collection = collect($transactions);

        $dayWiseCollection = [];

        $collection = $collection->groupBy(function ($transaction) {
            return Carbon::parse($transaction->created)->format('y-m-d');
        });

        foreach ($collection as $key => $arrayOfCollection) {
            $newKey = Carbon::parse($key)->englishDayOfWeek;
            foreach ($arrayOfCollection as $single_transaction) {
                $dayWiseCollection[$newKey]['date'] = Carbon::parse($single_transaction->created_at)->format('d-m-Y H:i:s');
                $dayWiseCollection[$newKey]['amount'] = $single_transaction->amount / 100;
            }
        }

        return $dayWiseCollection;
    }
}
