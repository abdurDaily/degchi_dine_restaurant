<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array(
            [
                'name' => 'TAKA',
                'code' => 'TK',
                'exchange_rate' => '1',
                'is_active' => true,
                'created_at' => '2024-05-25 06:18:13',
                'updated_at' => '2024-05-25 06:18:13'
            ],
            [
                'name' => 'Euro',
                'code' => 'Euro',
                'exchange_rate' => '0.95',
                'is_active' => true,
                'created_at' => '2024-05-25 06:18:13',
                'updated_at' => '2024-05-25 06:18:13'
            ]
        );


        collect($permissions)->each(function ($permission) {
            Currency::updateOrCreate(['name' => $permission['name']],$permission);
        });
    }
}
