<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_number',
        'image',
        'status',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    // active scope
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public static function generateUserNumber(): int
    {
        do {
            $number = random_int(10000000, 99999999);
        } while (static::where('user_number', $number)->exists());

        return $number;
    }

    public function defaultAdminRoute(): string
    {
        return route('dashboard');
    }

    public function hasDashboardWidgets(): bool
    {
        return $this->canAny([
            'orders-show',
            'members-show',
            'members-edit',
            'menu-list',
            'category-list',
            'offers-show',
            'reviews-show',
            'reviews-moderate',
            'branch-list',
        ]);
    }

    protected $appends = ['profile_image'];

    public function getProfileImageAttribute()
    {
        return $this->image ? asset('storage/images/profile/' . $this->image) : null;
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }
}
