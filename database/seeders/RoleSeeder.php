<?php

namespace Database\Seeders;

use id;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'User'],
        ];


        foreach ($roles as $role) {
            Role::updateOrCreate($role);
        }

        $superAdmins = [1];
        foreach ($superAdmins as $user) {
            $existing = DB::table('model_has_roles')->where([
                ['role_id', '=', 1],
                ['model_id', '=', $user],
                ['model_type', '=', 'App\Models\User']
            ])->first();

            if ($existing) {
                // Update the existing record based on the composite key (role_id, model_id, model_type)
                DB::table('model_has_roles')->where([
                    ['role_id', '=', 1],
                    ['model_id', '=', $user],
                    ['model_type', '=', 'App\Models\User']
                ])->update([
                    'role_id'       => 1,  // This can be omitted since it's the same
                    'model_id'      => $user,
                    'model_type'    => 'App\Models\User'
                ]);
            } else {
                // Insert a new record if it doesn't exist
                DB::table('model_has_roles')->insert([
                    'role_id'       => 1,
                    'model_id'      => $user,
                    'model_type'    => 'App\Models\User'
                ]);
            }

        }
    }
}
