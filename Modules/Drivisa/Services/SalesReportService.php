<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Course;
use Illuminate\Support\Facades\Cache;
use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\TransactionRepository;
use \Facades\Modules\Core\Services\Formatters\CurrencyFormatter;

class SalesReportService
{
    private PurchaseRepository $purchaseRepository;
    private TransactionRepository $transactionRepository;
    private PackageRepository $packageRepository;

    /**
     * @param PurchaseRepository $purchaseRepository
     * @param TransactionRepository $transactionRepository
     * @param PackageRepository $packageRepository
     */
    public function __construct(
        PurchaseRepository    $purchaseRepository,
        TransactionRepository $transactionRepository,
        PackageRepository     $packageRepository
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->transactionRepository = $transactionRepository;
        $this->packageRepository = $packageRepository;
    }

    public function allStats()
    {
        return [
            'daily' => $this->getStats('daily'),
            'weekly' => $this->getStats('weekly'),
            'monthly' => $this->getStats('monthly')
        ];
    }

    public function getStats($duration)
    {
        return [
            $this->getQuery($duration)->whereType(Purchase::TYPE['lesson'])->count(),
            $this->getPackage(4, $duration),
            $this->getPackage(6, $duration),
            $this->getPackage(8, $duration),
            $this->getPackage(10, $duration),
            $this->getQuery($duration)->whereType(Purchase::TYPE['g2_test'])->count(),
            $this->getQuery($duration)->whereType(Purchase::TYPE['g_test'])->count(),
            $this->getQuery($duration)->whereType(Purchase::TYPE['BDE'])->count(),
        ];
    }

    /**
     * @param $duration
     * @return mixed
     */
    private function getQuery($duration)
    {
        $query = $this->purchaseRepository
            ->query();

        if ($duration == 'daily') {
            $query->whereDate('created_at', today());
        } else if ($duration == 'weekly') {
            $query->whereDate('created_at', '<=', today())
                ->whereDate('created_at', '>=', today()->subDays(7));
        } else if ($duration == 'monthly') {
            $query->whereDate('created_at', '<=', today())
                ->whereDate('created_at', '>=', today()->subDays(30));
        }
        return $query;
    }

    public function report($data)
    {
        $year = (int)request('year', date('Y'));
        $start = "{$year}-01-01";
        $end = "{$year}-12-31";

        if ($data['start_from'] != 'null' && $data['end_at'] != 'null') {
            $start = $data['start_from'];
            $end = $data['end_at'];
        }

        $query = $this->getQuery($data['type']);
        $query->whereBetween('created_at', [$start, $end]);
        return $query->with(['transaction', 'trainee'])->latest()->get();
    }

    public function getYearlySalesReport()
    {
        $year = (int)request('year', date('Y'));
        $sales = [];
        $monthNames = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();
            $duration = 'monthly';
            $stats = $this->getMonthlyStats($duration, $start, $end);
            $sales[] = [
                'month' => $monthNames[$month - 1],
                'lesson' => $stats[0],
                'package_4_hour' => $stats[1],
                'package_6_hour' => $stats[2],
                'package_8_hour' => $stats[3],
                'package_10_hour' => $stats[4],
                'g2_test' => $stats[5],
                'g_test' => $stats[6],
                'BDE' => $stats[7],
            ];
        }
        return $sales;
    }

    private function getMonthlyStats($duration, $start = null, $end = null)
    {
        return [
            $this->getStatsQuery($duration, $start, $end)->whereType(Purchase::TYPE['lesson'])->count(),
            $this->getPackageStats(4, $duration, $start, $end),
            $this->getPackageStats(6, $duration, $start, $end),
            $this->getPackageStats(8, $duration, $start, $end),
            $this->getPackageStats(10, $duration, $start, $end),
            $this->getStatsQuery($duration, $start, $end)->whereType(Purchase::TYPE['g2_test'])->count(),
            $this->getStatsQuery($duration, $start, $end)->whereType(Purchase::TYPE['g_test'])->count(),
            $this->getStatsQuery($duration, $start, $end)->whereType(Purchase::TYPE['BDE'])->count()
        ];
    }

    private function getStatsQuery($duration, $start = null, $end = null)
    {
        $query = $this->purchaseRepository->query();

        if ($duration == 'monthly') {
            $query->whereBetween('created_at', [$start, $end]);
        }
        return $query;
    }

    public function getRevenue()
    {
        $year = (int)request('year', date('Y'));

        $total_revenue = $this->getTotalTransactionsAmount($year);
        $transfers = ($this->getTransfers($year)) / 100;

        // Ensure that actual_revenue is not negative
        $actual_revenue = max(0, $total_revenue - $transfers);

        return [
            'total_revenue' => CurrencyFormatter::getFormattedPrice($total_revenue),
            'transfer_amount' => CurrencyFormatter::getFormattedPrice($transfers),
            'actual_revenue' => CurrencyFormatter::getFormattedPrice($actual_revenue),
        ];
    }

    private function getTotalTransactionsAmount($year)
    {
        $purchases = Purchase::whereNotIn('type', [
            Purchase::TYPE['Bonus'],
            Purchase::TYPE['Bonus_BDE'],
        ])
            ->whereYear('created_at', $year)
            ->get();

        $transactionIds = $purchases->pluck('transaction_id')->toArray();

        return  $this->transactionRepository->whereIn('id', $transactionIds)->sum('amount');
    }

    private function getTransfers($year)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $transfers = \Stripe\Transfer::all([
            'created' => [
                'gte' => strtotime($year . '-01-01'),
                'lte' => strtotime($year . '-12-31')
            ]
        ]);

        $total = 0;
        foreach ($transfers->data as $transfer) {
            $total += $transfer->amount;
        }

        return $total;
    }

    private function getPackageStats($credit, $duration, $start = null, $end = null)
    {
        return $this->getStatsQuery($duration, $start, $end)
            ->whereHasMorph('purchaseable', [Course::class], function ($q) use ($credit) {
                $q->where('credit', $credit);
            })
            ->where('type', Purchase::TYPE['Package'])
            ->count();
    }

    private function getPackage($credit, $duration)
    {
        return $this->getQuery($duration)
            ->whereHasMorph('purchaseable', [Course::class], function ($q) use ($credit) {
                $q->where('credit', $credit);
            })
            ->where('type', Purchase::TYPE['Package'])
            ->count();
    }
}
