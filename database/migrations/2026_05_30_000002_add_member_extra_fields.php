<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (! Schema::hasColumn('members', 'dob')) {
                    $table->date('dob')->nullable()->after('email');
                }
                if (! Schema::hasColumn('members', 'marriage_date')) {
                    $table->date('marriage_date')->nullable()->after('dob');
                }
                if (! Schema::hasColumn('members', 'address')) {
                    $table->string('address')->nullable()->after('marriage_date');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (Schema::hasColumn('members', 'address')) {
                    $table->dropColumn('address');
                }
                if (Schema::hasColumn('members', 'marriage_date')) {
                    $table->dropColumn('marriage_date');
                }
                if (Schema::hasColumn('members', 'dob')) {
                    $table->dropColumn('dob');
                }
            });
        }
    }
};
