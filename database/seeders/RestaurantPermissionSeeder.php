<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RestaurantPermissionSeeder extends Seeder
{
    /**
     * Degchi Dine admin permissions grouped by restaurant module.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard-view', 'group' => 'dashboard', 'details' => 'View admin dashboard overview'],

            // Orders
            ['name' => 'orders-show', 'group' => 'orders', 'details' => 'View and track customer orders'],
            ['name' => 'orders-edit', 'group' => 'orders', 'details' => 'Update order status'],

            // Members
            ['name' => 'members-show', 'group' => 'members', 'details' => 'View membership cards and profiles'],
            ['name' => 'members-edit', 'group' => 'members', 'details' => 'Approve students, toggle status, sync purchases'],

            // Offers
            ['name' => 'offers-show', 'group' => 'offers', 'details' => 'View offers and promotions'],
            ['name' => 'offers-create', 'group' => 'offers', 'details' => 'Create new offers'],
            ['name' => 'offers-edit', 'group' => 'offers', 'details' => 'Edit and toggle offers'],
            ['name' => 'offers-delete', 'group' => 'offers', 'details' => 'Delete offers'],

            // Reviews
            ['name' => 'reviews-show', 'group' => 'reviews', 'details' => 'View customer reviews'],
            ['name' => 'reviews-moderate', 'group' => 'reviews', 'details' => 'Approve, reject, or delete reviews'],

            // Branches
            ['name' => 'branch-list', 'group' => 'branches', 'details' => 'View restaurant branches'],
            ['name' => 'branch-create', 'group' => 'branches', 'details' => 'Add new branches'],
            ['name' => 'branch-edit', 'group' => 'branches', 'details' => 'Edit branch details and delivery links'],
            ['name' => 'branch-delete', 'group' => 'branches', 'details' => 'Delete branches'],

            // Categories
            ['name' => 'category-list', 'group' => 'categories', 'details' => 'View menu categories'],
            ['name' => 'category-create', 'group' => 'categories', 'details' => 'Create menu categories'],
            ['name' => 'category-edit', 'group' => 'categories', 'details' => 'Edit menu categories'],
            ['name' => 'category-delete', 'group' => 'categories', 'details' => 'Delete menu categories'],

            // Menu items
            ['name' => 'menu-list', 'group' => 'menu', 'details' => 'View menu items and variations'],
            ['name' => 'menu-create', 'group' => 'menu', 'details' => 'Add menu items'],
            ['name' => 'menu-edit', 'group' => 'menu', 'details' => 'Edit menu items and variations'],
            ['name' => 'menu-delete', 'group' => 'menu', 'details' => 'Delete menu items'],

            // Frontend content
            ['name' => 'signature-platters-list', 'group' => 'frontend-content', 'details' => 'View signature platters on homepage'],
            ['name' => 'signature-platters-create', 'group' => 'frontend-content', 'details' => 'Add signature platters'],
            ['name' => 'signature-platters-edit', 'group' => 'frontend-content', 'details' => 'Edit signature platters'],
            ['name' => 'signature-platters-delete', 'group' => 'frontend-content', 'details' => 'Delete signature platters'],

            ['name' => 'facebook-reels-list', 'group' => 'frontend-content', 'details' => 'View Facebook reels section'],
            ['name' => 'facebook-reels-create', 'group' => 'frontend-content', 'details' => 'Add Facebook reels'],
            ['name' => 'facebook-reels-edit', 'group' => 'frontend-content', 'details' => 'Edit Facebook reels'],
            ['name' => 'facebook-reels-delete', 'group' => 'frontend-content', 'details' => 'Delete Facebook reels'],

            ['name' => 'about-show', 'group' => 'frontend-content', 'details' => 'View about section settings'],
            ['name' => 'about-edit', 'group' => 'frontend-content', 'details' => 'Update about section content'],

            ['name' => 'contact-show', 'group' => 'frontend-content', 'details' => 'View contact/location settings'],
            ['name' => 'contact-edit', 'group' => 'frontend-content', 'details' => 'Update contact/location content'],

            // Users & access
            ['name' => 'users-show', 'group' => 'users', 'details' => 'View admin users'],
            ['name' => 'users-create', 'group' => 'users', 'details' => 'Create admin users'],
            ['name' => 'users-edit', 'group' => 'users', 'details' => 'Edit users and approve accounts'],
            ['name' => 'users-delete', 'group' => 'users', 'details' => 'Delete admin users'],

            // Coupons
            ['name' => 'coupon-list', 'group' => 'coupons', 'details' => 'View coupons list'],
            ['name' => 'coupon-create', 'group' => 'coupons', 'details' => 'Create new coupons'],
            ['name' => 'coupon-edit', 'group' => 'coupons', 'details' => 'Edit coupons'],
            ['name' => 'coupon-delete', 'group' => 'coupons', 'details' => 'Delete coupons'],

            // System settings
            ['name' => 'theme-customization', 'group' => 'settings', 'details' => 'Customize admin theme'],
            ['name' => 'general-setting', 'group' => 'settings', 'details' => 'Logo, app name, SEO, SSLCommerz'],
            ['name' => 'email-setting', 'group' => 'settings', 'details' => 'Email configuration'],
            ['name' => 'pusher-setting', 'group' => 'settings', 'details' => 'Pusher / realtime settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                [
                    'group' => $permission['group'],
                    'details' => $permission['details'],
                ]
            );
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));
    }
}
