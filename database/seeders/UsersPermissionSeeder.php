<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array(
            [
                'name' => 'users-create',
                'guard_name' => 'web',
                'group' => 'users',
            ],
            [
                'name' => 'users-show',
                'guard_name' => 'web',
                'group' => 'users',
            ],
            [
                'name' => 'users-edit',
                'guard_name' => 'web',
                'group' => 'users',
            ],
            [
                'name' => 'users-delete',
                'guard_name' => 'web',
                'group' => 'users',
            ],
        );

        collect($permissions)->each(function($permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
                'group' => $permission['group']
            ]);
        });

    }
}
