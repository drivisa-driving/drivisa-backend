<?php

namespace Modules\Drivisa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Drivisa\Entities\FinalTestKey;

class FinalTestKeyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $header = FinalTestKey::create([
            'title' => 'Start'
        ]);
        FinalTestKey::create([
            'title' => 'unable to locate safety devices',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to observe-uses mirrors only',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of clutch/brake/accelerator/gears/steering',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to signal when leaving',
            'parent_id'=> $header->id
        ]);

        $header = FinalTestKey::create([
            'title' => 'Backing'
        ]);
        FinalTestKey::create([
            'title' => 'fails to look to rear before/while backing mirror only',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of clutch/brake/accelerator/gears/steering',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'turnabout: control/steering/Method/Observation',
            'parent_id'=> $header->id
        ]);

        $header = FinalTestKey::create([
            'title' => 'Driving Along'
        ]);
        FinalTestKey::create([
            'title' => 'follows or passes too closely/cuts in too soon',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'speed: too fast/too slow for conditions',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'improper choice of lane/straddles lanes/unmarked roadway',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to check blind spot/observe properly',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'lane change signal wrong/early/not given/not cancelled',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'right of way observance, ped./self/other vehicles ',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to use caution/obey pedestrians/crossovers/school crossing',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'emergency vehicles/rail road crossings',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of brake/accelerator/gears/steering/safety devices',
            'parent_id'=> $header->id
        ]);

        $header = FinalTestKey::create([
            'title' => 'Stop Park and Start On A Grade'
        ]);
        FinalTestKey::create([
            'title' => 'rolls back when parking or starting',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to angle wheels properly/fails to observe-use mirrors',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to set parking brake/fails to signal',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of (clutch/brake/accelerator)',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'gears/steering',
            'parent_id'=> $header->id
        ]);

        $header = FinalTestKey::create([
            'title' => 'Intersections / R.R. Crossings'
        ]);
        FinalTestKey::create([
            'title' => 'fails to observe properly/controlled/uncontrolled intersections',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to obey signs or signals/pavement markings ',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'late in slowing/slows too soon',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'stopping position: too soon or blocks crosswalk intersections',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'right of ways observance ped./self/other vehicles',
            'parent_id'=> $header->id
        ]);

        $header = FinalTestKey::create([
            'title' => 'Turns'
        ]);
        FinalTestKey::create([
            'title' => 'Signaling/wrong/early/late/not given/cancelled',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to get in proper position/lane/late into lane',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to check blind spot/observe properly ',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'right of way observance',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'ped/self/other vehicles/position',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'turns too wide/cuts corner/enters wrong lane',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'steering: method/control/recovery ',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'speed: too fast/too slow for conditions/enter/leave/impedes',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of: clutch/brake/accelerator/gears',
            'parent_id'=> $header->id
        ]);
        
        $header = FinalTestKey::create([
            'title' => 'Parking'
        ]);
        FinalTestKey::create([
            'title' => 'fails to observe-uses mirrors only/backing and leaving',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'hits: objects /other vehicles or climbs curbs',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect vehicle position',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'fails to signal/incorrect signal/leaving',
            'parent_id'=> $header->id
        ]);
        FinalTestKey::create([
            'title' => 'incorrect use of clutch/brake/accelerator/gears/steering',
            'parent_id'=> $header->id
        ]);
    }
}
