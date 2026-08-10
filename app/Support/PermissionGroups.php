<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PermissionGroups
{
    /**
     * Sidebar-aligned section order and labels.
     */
    public static function sections(): array
    {
        return [
            'main' => [
                'label' => 'Main',
                'icon' => 'ri-dashboard-line',
                'groups' => ['dashboard', 'orders', 'members', 'offers', 'reviews'],
            ],
            'catalog' => [
                'label' => 'Catalog',
                'icon' => 'ri-store-2-line',
                'groups' => ['branches', 'categories', 'menu', 'coupons'],
            ],
            'content' => [
                'label' => 'Content',
                'icon' => 'ri-layout-masonry-line',
                'groups' => ['frontend-content', 'party-bookings', 'blog'],
            ],
            'system' => [
                'label' => 'System',
                'icon' => 'ri-settings-3-line',
                'groups' => ['users', 'settings', 'currency'],
            ],
        ];
    }

    public static function groupLabels(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'orders' => 'Orders',
            'members' => 'Members',
            'offers' => 'Offers',
            'reviews' => 'Reviews',
            'branches' => 'Branch Management',
            'categories' => 'Categories',
            'menu' => 'Menu Management',
            'coupons' => 'Coupon Management',
            'frontend-content' => 'Frontend Content',
            'party-bookings' => 'Party Bookings',
            'blog' => 'Blog',
            'users' => 'Users',
            'settings' => 'Settings',
            'currency' => 'Currency',
        ];
    }

    public static function groupLabel(?string $group): string
    {
        $group = (string) $group;

        return self::groupLabels()[$group] ?? Str::headline(str_replace('-', ' ', $group));
    }

    /**
     * Sort a groupBy('group') collection into sidebar section order.
     */
    public static function organize(Collection $groupedPermissions): array
    {
        $organized = [];
        $usedGroups = [];

        foreach (self::sections() as $sectionKey => $section) {
            $sectionGroups = [];
            foreach ($section['groups'] as $groupKey) {
                if ($groupedPermissions->has($groupKey) && $groupedPermissions->get($groupKey)->isNotEmpty()) {
                    $sectionGroups[$groupKey] = $groupedPermissions->get($groupKey);
                    $usedGroups[] = $groupKey;
                }
            }

            if ($sectionGroups !== []) {
                $organized[$sectionKey] = [
                    'label' => $section['label'],
                    'icon' => $section['icon'],
                    'groups' => $sectionGroups,
                ];
            }
        }

        $remaining = $groupedPermissions->keys()->diff($usedGroups);
        if ($remaining->isNotEmpty()) {
            $otherGroups = [];
            foreach ($remaining as $groupKey) {
                if ($groupedPermissions->get($groupKey)?->isNotEmpty()) {
                    $otherGroups[$groupKey] = $groupedPermissions->get($groupKey);
                }
            }
            if ($otherGroups !== []) {
                $organized['other'] = [
                    'label' => 'Other',
                    'icon' => 'ri-folder-line',
                    'groups' => $otherGroups,
                ];
            }
        }

        return $organized;
    }

    public static function actionFromName(string $name): string
    {
        $parts = explode('-', $name);
        $action = Str::lower(end($parts) ?: $name);

        return match ($action) {
            'show', 'view', 'list' => 'View',
            'create', 'add' => 'Create',
            'edit', 'update' => 'Edit',
            'delete', 'destroy', 'remove' => 'Delete',
            'moderate' => 'Moderate',
            'customization' => 'Customize',
            'setting' => 'Settings',
            default => Str::headline($action),
        };
    }

    public static function actionBadgeClass(string $actionLabel): string
    {
        return match (Str::lower($actionLabel)) {
            'view' => 'bg-info-subtle text-info',
            'create' => 'bg-success-subtle text-success',
            'edit' => 'bg-warning-subtle text-warning',
            'delete' => 'bg-danger-subtle text-danger',
            'moderate' => 'bg-primary-subtle text-primary',
            'customize', 'settings' => 'bg-secondary-subtle text-secondary',
            default => 'bg-light text-body',
        };
    }
}
