<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\CurrencySettingSeeder;
use Database\Seeders\UsersPermissionSeeder;
use Database\Seeders\SettingPermissionSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\SignaturePlatterSeeder;
use Database\Seeders\ReviewSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                UserSeeder::class,
                RoleSeeder::class,
                RestaurantPermissionSeeder::class,
                CurrencySeeder::class,
                CurrencyPermissionSeeder::class,
                CurrencySettingSeeder::class,
                MenuSeeder::class,
                SignaturePlatterSeeder::class,
                ReviewSeeder::class,
            ]
        );
    }
}
