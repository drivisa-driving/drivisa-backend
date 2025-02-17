<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class WeeklyEarningTransformer extends JsonResource
{
    private float $total = 0.00;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $transactions = $this->transactions ?? null;
        [$collection, $total] = $this->currentCollection($transactions);
        return [
            'total' => CurrencyFormatter::getFormattedPrice($total), // output 0.0
            'current_collection' => $collection
        ];
    }

    private function currentCollection($transactions)
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        $dayWiseCollection = [];
        $total = 0;

        $this->calculateAndBuild($transactions, $days, $dayWiseCollection, $total);

        $dayWiseCollection = $this->getDayWiseCollection($days, $dayWiseCollection);

        $sortedCollection = $this->getCollection($dayWiseCollection, $days);

        return [$sortedCollection, ($total / 100)];
    }

    /**
     * @param array $dayWiseCollection
     * @param array $days
     * @return \Illuminate\Support\Collection
     */
    private function getCollection(array $dayWiseCollection, array $days): \Illuminate\Support\Collection
    {
        $sortedCollection = collect($dayWiseCollection)->sortBy(function ($item, $key) use ($days) {
            return array_search($key, $days);
        });
        return $sortedCollection;
    }

    /**
     * @param array $days
     * @param array $dayWiseCollection
     * @return array
     */
    private function getDayWiseCollection(array $days, array $dayWiseCollection): array
    {
        foreach ($days as $day) {
            if (!array_key_exists($day, $dayWiseCollection)) {
                $dayWiseCollection[$day]['date'] = null;
                $dayWiseCollection[$day]['amount'] = CurrencyFormatter::getFormattedPrice(); // output 0.00
            }
        }
        return $dayWiseCollection;
    }

    /**
     * @param $transactions
     * @param array $days
     * @param array $dayWiseCollection
     * @param $total
     * @return void
     */
    private function calculateAndBuild($transactions, array $days, array &$dayWiseCollection, &$total): void
    {
        if ($transactions) {
            foreach ($transactions as $transaction) {
                $english_week_day = Carbon::parse($transaction->created)->timezone('Canada/Eastern')->englishDayOfWeek; // Monday, Tuesday

                foreach ($days as $day) {
                    if (isset($dayWiseCollection[$day]['amount'])) {
                        $amount = (float)$dayWiseCollection[$day]['amount'] + ((float)$transaction->amount / 100);
                    } else {
                        $amount = (float)($transaction->amount / 100);
                    }
                    if ($day == $english_week_day) {
                        $dayWiseCollection[$day]['date'] = Carbon::parse($transaction->created)->timezone('Canada/Eastern')->format('d-m-Y H:i a');
                        $dayWiseCollection[$day]['amount'] = CurrencyFormatter::getFormattedPrice($amount);
                    }
                }
                $total += $transaction->amount;
            }
        }
    }
}
