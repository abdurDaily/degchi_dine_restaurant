<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Member;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first member or create one
        $member = Member::first();
        
        if (!$member) {
            $this->command->info('No members found. Skipping review seeding.');
            return;
        }

        // Create sample reviews
        $reviews = [
            [
                'member_id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'rating' => 5,
                'title' => 'Excellent Service!',
                'comment' => 'The food quality is outstanding and the service is impeccable. Highly recommend this place to everyone.',
                'image' => $member->profile_image_path,
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 1,
            ],
            [
                'member_id' => $member->id,
                'name' => 'Test User 2',
                'email' => null, // Test NULL email
                'rating' => 4,
                'title' => 'Great Experience',
                'comment' => 'Loved the ambiance and food variety. Will definitely come back again.',
                'image' => $member->profile_image_path,
                'status' => 'pending',
            ],
            [
                'member_id' => $member->id,
                'name' => 'Test User 3',
                'email' => 'test@example.com',
                'rating' => 5,
                'title' => 'Amazing!',
                'comment' => 'Best dining experience I have had. The traditional recipes are authentic and delicious.',
                'image' => $member->profile_image_path,
                'status' => 'approved',
                'approved_at' => now()->subDays(2),
                'approved_by' => 1,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }

        $this->command->info('Review seeding completed!');
        $this->command->info('Created 3 test reviews:');
        $this->command->info('  - 2 Approved');
        $this->command->info('  - 1 Pending');
        $this->command->info('Visit: http://127.0.0.1:8000/admin/reviews');
    }
}
