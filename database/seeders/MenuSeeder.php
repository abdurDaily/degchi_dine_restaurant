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
        $branches = [
            [
                'name' => 'Halishahar Branch',
                'location' => 'Boropool Circle, Kaptan Villa, Halishahar, Chittagong',
                'phone' => '01898795400',
            ],
            [
                'name' => 'Chawkbazar Branch',
                'location' => 'College Road, Chawkbazar, Chittagong',
                'phone' => '01615445566',
            ],
            [
                'name' => 'GEC Branch',
                'location' => 'CDA Avenue, Bata Goli, GEC, Chittagong',
                'phone' => '01618445566',
            ],
        ];

        $branchModels = [];
        foreach ($branches as $branch) {
            $branchModels[] = Branch::updateOrCreate(['name' => $branch['name']], $branch);
        }

        $defaultBranch = $branchModels[0];

        // 2. Seed Categories
        $categories = [
            [
                'name' => 'Kacchi & Biryani',
                'slug' => 'kacchi-biryani',
                'description' => 'Our signature slow-cooked Dum Kacchi and aromatic Biryani items.',
                'image' => 'assets/placeholder/placeholder.png',
                'status' => 1,
            ],
            [
                'name' => 'Signature Platters',
                'slug' => 'signature-platters',
                'description' => 'Carefully curated selections perfect for family sharing.',
                'image' => 'assets/placeholder/placeholder.png',
                'status' => 1,
            ],
            [
                'name' => 'Sides & Appetizers',
                'slug' => 'sides-appetizers',
                'description' => 'Crispy and savory complements for your main course.',
                'image' => 'assets/placeholder/placeholder.png',
                'status' => 1,
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'description' => 'Sweet royal treats to end your meal on a perfect note.',
                'image' => 'assets/placeholder/placeholder.png',
                'status' => 1,
            ],
            [
                'name' => 'Refreshing Drinks',
                'slug' => 'refreshing-drinks',
                'description' => 'Cold beverages, signature Borhani, and traditional drinks.',
                'image' => 'assets/placeholder/placeholder.png',
                'status' => 1,
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'branch_id' => $defaultBranch->id,
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'image' => $cat['image'],
                    'status' => $cat['status'],
                ]
            );
        }

        // 3. Seed Menu Items & Variations
        $menuItems = [
            [
                'category_slug' => 'kacchi-biryani',
                'name' => 'Mutton Kacchi',
                'description' => 'Fragrant basmati rice layered with marinated mutton, saffron, and signature spices.',
                'variations' => [
                    [
                        'name' => '1:2 Portion',
                        'price' => 420.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                    [
                        'name' => '1:3 Portion',
                        'price' => 620.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                ]
            ],
            [
                'category_slug' => 'kacchi-biryani',
                'name' => 'Purān Dhākār Kacchi',
                'description' => 'Old Dhaka style traditional beef kacchi cooked in slow copper vessel.',
                'variations' => [
                    [
                        'name' => 'Single Pack',
                        'price' => 340.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                    [
                        'name' => 'Double Pack',
                        'price' => 650.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                ]
            ],
            [
                'category_slug' => 'kacchi-biryani',
                'name' => 'Chicken Biryani',
                'description' => 'Mildly spiced, aromatic biryani served with tender chicken piece and boiled egg.',
                'variations' => [
                    [
                        'name' => 'Regular',
                        'price' => 290.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ]
                ]
            ],
            [
                'category_slug' => 'signature-platters',
                'name' => 'Kacchi Biryani Duo Platter',
                'description' => 'Mutton Kacchi served with Chicken roast, Borhani, and Jorda for two people.',
                'variations' => [
                    [
                        'name' => 'Serve 2',
                        'price' => 890.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ]
                ]
            ],
            [
                'category_slug' => 'sides-appetizers',
                'name' => 'Shahi Chicken Roast',
                'description' => 'Crispy fried chicken slow-braised in a rich caramelised onion and yogurt gravy.',
                'variations' => [
                    [
                        'name' => '1 Piece',
                        'price' => 140.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ]
                ]
            ],
            [
                'category_slug' => 'desserts',
                'name' => 'Shahi Jorda',
                'description' => 'Sweet saffron rice garnished with baby sweets, nuts, and raisins.',
                'variations' => [
                    [
                        'name' => 'Cup',
                        'price' => 80.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ]
                ]
            ],
            [
                'category_slug' => 'refreshing-drinks',
                'name' => 'Signature Borhani',
                'description' => 'Traditional spiced sour yogurt drink flavored with mint, coriander, and mustard seed.',
                'variations' => [
                    [
                        'name' => 'Glass',
                        'price' => 50.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                    [
                        'name' => '1 Liter Bottle',
                        'price' => 180.00,
                        'image' => 'assets/placeholder/placeholder.png',
                    ],
                ]
            ],
        ];

        foreach ($menuItems as $item) {
            $catModel = $categoryModels[$item['category_slug']];
            $menu = Menu::updateOrCreate(
                ['slug' => Str::slug($item['name']) . '-' . $catModel->id],
                [
                    'category_id' => $catModel->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_available' => 1,
                ]
            );

            // Delete old variations to avoid duplicates
            $menu->variations()->delete();

            foreach ($item['variations'] as $v) {
                MenuVariation::create([
                    'menu_id' => $menu->id,
                    'name' => $v['name'],
                    'price' => $v['price'],
                    'image' => $v['image'],
                ]);
            }
        }
    }
}
