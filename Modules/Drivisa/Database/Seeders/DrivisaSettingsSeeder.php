<?php

namespace Modules\Drivisa\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Setting\Entities\Setting;

class DrivisaSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Setting::insert([
            [
                'name' => 'drivisa::lesson_cost',
                'plainValue' => '50',
                'isTranslatable' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'drivisa::commission',
                'plainValue' => '15',
                'isTranslatable' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'drivisa::lesson_tax',
                'plainValue' => '5',
                'isTranslatable' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
