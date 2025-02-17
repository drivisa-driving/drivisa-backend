<?php

namespace Modules\Drivisa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Drivisa\Entities\MarkingKey;

class MarkingKeyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

       $markingKeys = [
           [ 'title' => 'Circle Check' ],
           [ 'title' => 'Dash/Start/Controls' ],
           [ 'title' => 'Moving/Braking' ],
           [ 'title' => 'Position In Line' ],
           [ 'title' => 'Hand Over Hand Steering Control' ],
           [ 'title' => 'Turn Recovery' ],
           [ 'title' => '3 Stopping Positions' ],
           [ 'title' => 'Signals (early/late)' ],
           [ 'title' => 'Scanning (L.C.L.R)' ],
           [ 'title' => 'Visual Skills 20-30 Sec' ],
           [ 'title' => 'Right/Left Turns' ],
           [ 'title' => 'Proper Speed Conditions' ],
           [ 'title' => 'Following Distance' ],
           [ 'title' => 'Staggering Vehicles ' ],
           [ 'title' => 'Yield Signs / Merge' ],
           [ 'title' => 'Lane Changes' ],
           [ 'title' => 'Backing & Front in Parking' ],
           [ 'title' => 'Uphill & Downhill Parking' ],
           [ 'title' => 'Parallel Parking' ],
           [ 'title' => 'Roadside Stop' ],
           [ 'title' => '2 & 3 Point Turns' ],
           [ 'title' => 'Rail Road Crossings' ],
           [ 'title' => 'U Turns/Roundabout' ],
           [ 'title' => 'One Way Streets' ],
           [ 'title' => 'City Driving' ],
           [ 'title' => 'Proper Use of See-Think-Do' ],
           [ 'title' => '80Km Highways' ],
           [ 'title' => '100km Highways' ],
       ]; 

        foreach($markingKeys as $markingKey){
            MarkingKey::create($markingKey);
        }
    }
}
