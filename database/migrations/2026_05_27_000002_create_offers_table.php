<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->string('applicable_to')->default('membership');
            $table->decimal('min_total', 10, 2)->nullable();
            $table->boolean('is_first_order')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('offers')->insert([
            [
                'name' => 'Membership First Order',
                'description' => '30% discount on first order for new membership holders.',
                'discount_percent' => 30,
                'applicable_to' => 'membership',
                'min_total' => null,
                'is_first_order' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student Membership First Order',
                'description' => '35% first-order discount when student card is shown.',
                'discount_percent' => 35,
                'applicable_to' => 'student',
                'min_total' => null,
                'is_first_order' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Golden Upgrade Threshold',
                'description' => 'Golden card upgrade when an order total reaches ৳2,000.',
                'discount_percent' => 0,
                'applicable_to' => 'golden',
                'min_total' => 2000,
                'is_first_order' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
