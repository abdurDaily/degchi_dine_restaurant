<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'author_id' => User::factory(),
            'blog_category_id' => BlogCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'image' => null,
            'is_active' => true,
            'comments_enabled' => true,
            'view_count' => fake()->numberBetween(0, 500),
        ];
    }
}
