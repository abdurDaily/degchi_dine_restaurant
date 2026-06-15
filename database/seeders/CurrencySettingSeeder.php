<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'setting_group' => 'general_setting',
            'key' => 'currency',
            'value' => 1,
            'user_id' => 633,
        ];
        Setting::updateOrCreate($data);
    }
}
