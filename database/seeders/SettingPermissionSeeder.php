<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['name' => 'theme-customization', 'details' => 'customize the theme', 'group' => 'settings'],
            ['name' => 'general-setting', 'details' => 'for logo, app name', 'group' => 'settings'],
            ['name' => 'email-setting', 'details' => 'for logo, app name', 'group' => 'settings'],
        ];

        collect($settings)->each(function ($item) {
           Permission::updateOrCreate(['name' => $item['name']],$item);
        });
    }
}
