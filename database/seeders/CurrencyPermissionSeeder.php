<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CurrencyPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array(
            [
                'name' => 'currency-create',
                'guard_name' => 'web',
                'group' => 'currency',
            ],
            [
                'name' => 'currency-show',
                'guard_name' => 'web',
                'group' => 'currency',
            ],
            [
                'name' => 'currency-edit',
                'guard_name' => 'web',
                'group' => 'currency',
            ],
            [
                'name' => 'currency-delete',
                'guard_name' => 'web',
                'group' => 'currency',
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
