<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Carbon\Carbon;
use Spatie\DbDumper\Databases\MySql;
use Modules\Core\Http\Controllers\Api\ApiBaseController;

class DatabaseDumpController extends ApiBaseController
{
    public function exportDatabase()
    {
        try {
            $filename = 'drivisa-database-' . Carbon::now()->format('Y-m-d') . '.sql';
            $tempFilePath = sys_get_temp_dir() . '/' . $filename;

            MySql::create()
                ->setDbName(env('DB_DATABASE'))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'))
                ->dumpToFile($tempFilePath);

            return response()->download($tempFilePath, $filename)->deleteFileAfterSend();
        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}
