<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Branches
        // $branches = [
        //     [
        //         'name' => 'Halishahar Branch',
        //         'location' => 'Boropool Circle, Kaptan Villa, Halishahar, Chittagong',
        //         'phone' => '01898795400',
        //     ],
        //     [
        //         'name' => 'Chawkbazar Branch',
        //         'location' => 'College Road, Chawkbazar, Chittagong',
        //         'phone' => '01615445566',
        //     ],
        //     [
        //         'name' => 'GEC Branch',
        //         'location' => 'CDA Avenue, Bata Goli, GEC, Chittagong',
        //         'phone' => '01618445566',
        //     ],
        // ];

        $menus = [
    // Kacchi Items
    [
        'category_id' => 1,
        'name' => 'Basmati Mutton Kacchi',
        'slug' => 'basmati-mutton-kacchi',
        'description' => 'Traditional Kacchi biryani made with fragrant basmati rice and tender mutton.',
        'price' => 330,
    ],
    [
        'category_id' => 1,
        'name' => 'Mutton Leg Roast Kacchi',
        'slug' => 'mutton-leg-roast-kacchi',
        'description' => 'Premium Kacchi biryani served with a juicy whole mutton leg piece.',
        'price' => 410,
    ],
    [
        'category_id' => 1,
        'name' => 'shahi Beef Kacchi',
        'slug' => 'shahi-beef-kacchi',
        'description' => 'Flavorful Kacchi prepared with tender beef and aromatic spices.',
        'price' => 280,
    ],
    [
        'category_id' => 1,
        'name' => 'shahi Chicken Kacchi',
        'slug' => 'shahi-chicken-kacchi',
        'description' => 'Classic chicken Kacchi served with potatoes and traditional spices.',
        'price' => 250,
    ],
    [
        'category_id' => 1,
        'name' => 'Nabab Biryani',
        'slug' => 'nabab-biryani',
        'description' => 'Special biryani featuring chicken and a mutton leg piece.',
        'price' => 350,
    ],

    // Hyderabadi Items
    [
        'category_id' => 1,
        'name' => 'Chicken Hyderabadi Biryani',
        'slug' => 'chicken-hyderabadi-biryani',
        'description' => 'Authentic Hyderabadi-style biryani cooked with tender chicken and fragrant rice.',
        'price' => 320,
    ],
    [
        'category_id' => 1,
        'name' => 'Beef Hyderabadi Biryani',
        'slug' => 'beef-hyderabadi-biryani',
        'description' => 'Rich and spicy Hyderabadi biryani made with succulent beef.',
        'price' => 340,
    ],
    [
        'category_id' => 1,
        'name' => 'Mutton Hyderabadi Biryani',
        'slug' => 'mutton-hyderabadi-biryani',
        'description' => 'Classic Hyderabadi biryani featuring slow-cooked mutton and aromatic rice.',
        'price' => 370,
    ],

    // Biryani Items
    [
        'category_id' => 1,
        'name' => 'Orush Biryani',
        'slug' => 'orush-biryani',
        'description' => 'A delicious house-special biryani prepared with traditional flavors.',
        'price' => 200,
    ],
    [
        'category_id' => 1,
        'name' => 'Afgani Beef Tehari',
        'slug' => 'afgani-beef-tehari',
        'description' => 'Fragrant beef tehari cooked with ghee and aromatic spices.',
        'price' => 240,
    ],
    [
        'category_id' => 1,
        'name' => 'Chicken Dum Biryani',
        'slug' => 'chicken-dum-biryani',
        'description' => 'Slow-cooked dum biryani made with tender chicken.',
        'price' => 200,
    ],
    [
        'category_id' => 1,
        'name' => 'Morog Polao',
        'slug' => 'morog-polao',
        'description' => 'Traditional Bengali polao served with  chicken and ghee.',
        'price' => 180,
    ],
    [
        'category_id' => 1,
        'name' => 'Irani Morog Polao (Sonali)',
        'slug' => 'irani-morog-polao',
        'description' => 'A Persian-inspired chicken polao with rich flavors and spices.',
        'price' => 240,
    ],
    [
        'category_id' => 1,
        'name' => ' Fried Rice Package',
        'slug' => 'fried-rice',
        'description' => 'Delicious fried rice prepared with premium ingredients and seasonings.',
        'price' => 280,
    ],

    // Khichuri Items
    [
        'category_id' => 1,
        'name' => 'Special Chicken Khichuri',
        'slug' => 'special-chicken-khichuri',
        'description' => 'Comforting khichuri cooked with chicken and aromatic spices.',
        'price' => 240,
    ],
    [
        'category_id' => 1,
        'name' => 'Shahi Beef Khichuri',
        'slug' => 'shahi-beef-khichuri',
        'description' => 'Traditional khichuri served with tender beef and flavorful rice.',
        'price' => 260,
    ],
    [
        'category_id' => 1,
        'name' => 'Sorisha Mutton Khichuri',
        'slug' => 'sorisha-mutton-khichuri',
        'description' => 'Nutritious khichuri combined with vegetables and mutton.',
        'price' => 280,
    ],
    [
        'category_id' => 1,
        'name' => 'Sorisha Hash Khichuri',
        'slug' => 'sorisha-hash-khichuri',
        'description' => 'Traditional khichuri prepared with tender duck meat and spices.',
        'price' => 300,
    ],

    // Drinks & Desserts
    [
        'category_id' => 1,
        'name' => 'Badam Sharbat',
        'slug' => 'badam-sharbat',
        'description' => 'Refreshing almond-based drink served chilled.',
        'price' => 90,
    ],
    [
        'category_id' => 1,
        'name' => 'Borhani',
        'slug' => 'borhani',
        'description' => 'Traditional spiced yogurt drink, perfect with biryani and Kacchi.',
        'price' => 60,
    ],
    [
        'category_id' => 1,
        'name' => 'Firni',
        'slug' => 'firni',
        'description' => 'Creamy rice pudding delicately flavored with cardamom and nuts.',
        'price' => 50,
    ],
    [
        'category_id' => 1,
        'name' => 'Mixed Fruit Dessert',
        'slug' => 'mixed-fruit-dessert',
        'description' => 'Refreshing blend of seasonal fruits served chilled.',
        'price' => 120,
    ],
    [
        'category_id' => 1,
        'name' => 'Premium Falooda',
        'slug' => 'premium-falooda',
        'description' => 'Rich and refreshing dessert drink layered with vermicelli, jelly, and ice cream.',
        'price' => 250,
    ],
];


    }
}
