<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'dob',
        'marriage_date',
        'address',
        'unique_card_number',
        'last4',
        'is_student',
        'approval_status',
        'profile_image_path',
        'student_card_path',
        'type',
        'status',
        'total_purchase',
        'first_order_discount_used',
        'expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'first_order_discount_used' => 'boolean',
        'total_purchase' => 'decimal:2',
        'dob' => 'date',
        'marriage_date' => 'date',
        'expires_at' => 'date',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Normalize phone to last 10 digits for comparison.
     */
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }

        return $digits;
    }

    /**
     * Check if a phone number is already registered (normalized match).
     */
    public static function phoneExists(string $phone): bool
    {
        $target = self::normalizePhone($phone);

        if ($target === '') {
            return false;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->contains(fn (Member $member) => self::normalizePhone($member->phone) === $target);
    }

    public static function findByPhoneOrCard(string $login): ?self
    {
        $login = trim($login);

        $member = static::where('unique_card_number', $login)->first();
        if ($member) {
            return $member;
        }

        $target = self::normalizePhone($login);
        if ($target === '') {
            return null;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->first(fn (Member $member) => self::normalizePhone($member->phone) === $target);
    }
}
