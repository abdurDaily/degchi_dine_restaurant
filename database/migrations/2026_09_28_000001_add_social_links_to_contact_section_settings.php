<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;
        $now = now();

        DB::table('settings')->insertOrIgnore([
            [
                'setting_group' => 'contact_section',
                'key' => 'contact_facebook_group_url',
                'value' => 'https://www.facebook.com/groups/1347645484217487',
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        cache()->forget('home_contact_settings');
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('setting_group', 'contact_section')
            ->whereIn('key', ['contact_facebook_group_url', 'contact_twitter_url', 'contact_tripadvisor_url'])
            ->delete();

        cache()->forget('home_contact_settings');
    }
};
