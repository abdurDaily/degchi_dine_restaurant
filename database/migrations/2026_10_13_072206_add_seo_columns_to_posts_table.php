<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title', 255)->nullable()->after('image');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
            $table->string('meta_keywords', 255)->nullable()->after('meta_description');
            $table->string('og_title', 255)->nullable()->after('meta_keywords');
            $table->string('og_description', 500)->nullable()->after('og_title');
            $table->string('og_image', 500)->nullable()->after('og_description');
            $table->string('twitter_card', 50)->nullable()->after('og_image');
            $table->string('twitter_title', 255)->nullable()->after('twitter_card');
            $table->string('twitter_description', 500)->nullable()->after('twitter_title');
            $table->string('twitter_image', 500)->nullable()->after('twitter_description');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image',
            ]);
        });
    }
};
