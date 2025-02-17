<?php

namespace Modules\Drivisa\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Setting\Entities\Setting;

class DrivisaDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DrivisaIndicatorsSeeder::class);
        //$this->call(DrivisaSettingsSeeder::class);
    }
}
