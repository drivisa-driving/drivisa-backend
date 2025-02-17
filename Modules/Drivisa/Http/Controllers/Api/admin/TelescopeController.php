<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Laravel\Telescope\Storage\EntryModel;

class TelescopeController extends ApiBaseController
{
    public function clearTelescopeEntries()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Disable foreign key checks
            DB::table('telescope_entries_tags')->truncate();
            DB::table('telescope_entries')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Enable foreign key checks
            return response()->json(['message' => 'Telescope entries cleared successfully']);
        } catch (\Exception $exception) {
            return $this->errorMessage($exception->getMessage(), $exception->getCode());
        }
    }
}