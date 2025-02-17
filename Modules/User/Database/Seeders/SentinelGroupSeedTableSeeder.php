<?php

namespace Modules\User\Database\Seeders;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SentinelGroupSeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $groups = Sentinel::getRoleRepository();

        // Create an Admin group
        $groups->createModel()->create(
            [
                'name' => 'Admin',
                'slug' => 'admin',
            ]
        );

        // Create an Users group
        $groups->createModel()->create(
            [
                'name' => 'User',
                'slug' => 'user',
            ]
        );

        // Save the permissions
        $group = Sentinel::findRoleBySlug('admin');
        $group->permissions = [
            /* Roles */
            'user.roles.access' => true,
            'user.roles.list' => true,
            'user.roles.create' => true,
            'user.roles.edit' => true,
            'user.roles.destroy' => true,
            /* Users */
            'user.users.access' => true,
            'user.users.list' => true,
            'user.users.create' => true,
            'user.users.edit' => true,
            'user.users.destroy' => true,
            /* Settings */
            'setting.settings.index' => true,
            'setting.settings.edit' => true,
        ];
        $group->save();
    }
}
