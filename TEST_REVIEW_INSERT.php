<?php
/**
 * Quick Test Script to Insert a Review
 * Run this in your browser: /TEST_REVIEW_INSERT.php
 * Or from command line: php TEST_REVIEW_INSERT.php
 */

// Start Laravel app
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create a minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$app->make('Illuminate\Contracts\Http\Kernel')->handle($request);

// Now we can use models
use App\Models\Review;
use App\Models\Member;

echo "Testing Review System...\n\n";

// Check if reviews table exists
try {
    $count = Review::count();
    echo "✓ Reviews table exists\n";
    echo "  Current review count: $count\n\n";
} catch (\Exception $e) {
    echo "✗ Error accessing reviews table:\n";
    echo "  " . $e->getMessage() . "\n\n";
    die();
}

// Check members
$memberCount = Member::count();
echo "✓ Members table exists\n";
echo "  Current member count: $memberCount\n\n";

// Try to insert a test review
if ($memberCount > 0) {
    try {
        $member = Member::first();
        echo "Found member: {$member->name} (ID: {$member->id})\n";
        
        // Check if member already has a review
        $existingReview = Review::where('member_id', $member->id)->first();
        if ($existingReview) {
            echo "Member already has review (ID: {$existingReview->id})\n";
            echo "Review details:\n";
            echo "  Title: " . ($existingReview->title ?? 'N/A') . "\n";
            echo "  Status: " . $existingReview->status . "\n";
        } else {
            echo "No reviews found for this member\n";
            echo "Attempting to insert test review...\n\n";
            
            $review = Review::create([
                'member_id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'rating' => 5,
                'title' => 'Test Review - Dashboard Testing',
                'comment' => 'This is a test review for dashboard functionality. If you can see this, the dashboard is working correctly!',
                'image' => $member->profile_image_path,
                'status' => 'pending',
            ]);
            
            echo "✓ Review created successfully!\n";
            echo "  ID: {$review->id}\n";
            echo "  Name: {$review->name}\n";
            echo "  Email: " . ($review->email ?? 'NULL') . "\n";
            echo "  Rating: {$review->rating}\n";
            echo "  Status: {$review->status}\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error inserting review:\n";
        echo "  " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ No members found in database\n";
    echo "  Please create at least one member first\n";
}

echo "\n\nYou can now check the dashboard at:\n";
echo "http://127.0.0.1:8000/admin/reviews\n";
?>
