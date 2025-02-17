<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Services\SalesReportService;
use Modules\Drivisa\Transformers\AdminSaleReportTransformer;

class SalesReportController extends ApiBaseController
{
    private SalesReportService $salesReportService;

    /**
     * @param SalesReportService $salesReportService
     */
    public function __construct(
        SalesReportService $salesReportService
    ) {
        $this->salesReportService = $salesReportService;
    }

    public function index(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->salesReportService->allStats();
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function report(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return AdminSaleReportTransformer::collection($this->salesReportService->report($request->all()));
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getYearlySalesReport(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->salesReportService->getYearlySalesReport();
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getRevenue(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return $this->salesReportService->getRevenue();
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
