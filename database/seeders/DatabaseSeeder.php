<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\CurrencySettingSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingPermissionSeeder;
use Database\Seeders\SignaturePlatterSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UsersPermissionSeeder;
use Illuminate\Database\Seeder;

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
                OrderSeeder::class,
                MenuSeeder::class,
                SignaturePlatterSeeder::class,
                ReviewSeeder::class,
            ]
        );
    }
}
