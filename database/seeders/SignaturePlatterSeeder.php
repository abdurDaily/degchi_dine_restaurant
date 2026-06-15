<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignaturePlatterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('signature_platters')->insert([
            [
                'title' => 'Flavors To Brighten Your Afternoon!',
                'subtitle' => 'A MID-DAY FEAST OF INDIA',
                'description' => 'Choose from a delightful balance of vegetarian and non-vegetarian dishes — Perfect for a satisfying, flavorful lunch break.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80',
                'menu_card_image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80',
                'features' => json_encode([
                    'Veg Lunch Special: Paneer Butter Masala, Dal Tadka, Mixed Vegetable Curry Served with Basmati Rice & Naan',
                    'Non-Veg Lunch Special: Butter Chicken or Chicken Curry, Lamb Rogan Josh, Fish Masala'
                ]),
                'status' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Ultimate Healthy Power Bowl',
                'subtitle' => 'FRESH & REVITALIZING',
                'description' => 'A perfect mix of greens, grains, and proteins to keep your energy up throughout the day without feeling heavy.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80',
                'menu_card_image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80',
                'features' => json_encode([
                    'Base: Organic Quinoa, Fresh Kale, and Roasted Sweet Potatoes',
                    'Toppings: Sliced Avocado, Cherry Tomatoes, Toasted Seeds, and our House Vinaigrette'
                ]),
                'status' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Grand Dinner Special',
                'subtitle' => 'EVENING INDULGENCE',
                'description' => 'A rich and fulfilling spread designed for the perfect evening dining experience with friends and family.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&q=80',
                'menu_card_image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&q=80',
                'features' => json_encode([
                    'Premium Curries: Mutton Korma, Shahi Paneer, and Dal Makhani',
                    'Accompaniments: Garlic Naan, Jeera Rice, and Mint Raita'
                ]),
                'status' => 1,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Chef\'s Exclusive Choice',
                'subtitle' => 'CURATED PERFECTION',
                'description' => 'Let our head chef take you on a culinary journey with our most premium, hand-selected seasonal dishes.',
                'thumbnail_image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500&q=80',
                'menu_card_image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500&q=80',
                'features' => json_encode([
                    'Signature Mains: Tandoori Lobster, Truffle Butter Chicken',
                    'Pairings: Saffron Pilaf, Assorted Artisan Breads'
                ]),
                'status' => 1,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
