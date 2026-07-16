<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $permissions = array(
            [
                'name' => 'orders-show',
                'guard_name' => 'web',
                'group' => 'orders',
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
