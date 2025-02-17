<?php

namespace Modules\Drivisa\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Drivisa\Entities\EvaluationIndicator;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\Sentinel\User;

class DrivisaIndicatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $user = User::first();;
        EvaluationIndicator::insert([
            [
                'title' => 'Circle Check',
                'description' => 'Circle Check',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Dash/Start/Controls',
                'description' => 'Dash/Start/Controls',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Moving/Braking',
                'description' => 'Moving/Braking',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Position In Line',
                'description' => 'Position In Line',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Hand Over Hand Steering Control',
                'description' => 'Hand Over Hand Steering Control',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Turn Recovery',
                'description' => 'Turn Recovery',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => '3 Stopping Positions',
                'description' => '3 Stopping Positions',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Signals (early/late)',
                'description' => 'Signals (early/late)',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Scanning (L.C.L.R)',
                'description' => 'Scanning (L.C.L.R)',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Visual Skills 20-30 Sec',
                'description' => 'Visual Skills 20-30 Sec',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Right/Left Turns',
                'description' => 'Right/Left Turns',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Proper Speed Conditions',
                'description' => 'Proper Speed Conditions',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Following Distance',
                'description' => 'Following Distance',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Staggering Vehicles',
                'description' => 'Staggering Vehicles',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Yield Signs / Merge',
                'description' => 'Yield Signs / Merge',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Lane Changes',
                'description' => 'Lane Changes',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Backing & Front in Parking',
                'description' => 'Backing & Front in Parking',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Uphill & Downhill Parking',
                'description' => 'Uphill & Downhill Parking',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Parallel Parking',
                'description' => 'Parallel Parking',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Roadside Stop',
                'description' => 'Roadside Stop',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => '2 & 3 Point Turns',
                'description' => '2 & 3 Point Turns',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Rail Road Crossings',
                'description' => 'Rail Road Crossings',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'U Turns/Roundabout',
                'description' => 'U Turns/Roundabout',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'One Way Streets',
                'description' => 'One Way Streets',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'City Driving',
                'description' => 'City Driving',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => 'Proper Use of See-Think-Do',
                'description' => 'Proper Use of See-Think-Do',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => '80Km Highways',
                'description' => '80Km Highways',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'title' => '100km Highways',
                'description' => '100km Highways',
                'points' => 10,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        // $this->call("OthersTableSeeder");
    }
}
