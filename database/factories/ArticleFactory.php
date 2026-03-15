<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => 'seeder',
            'external_id' => Str::uuid(),
            'url' => $this->faker->unique()->url(),
            'image_url' => 'https://picsum.photos/1200/600?random=' . rand(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'published_at' => now()->subMinutes(rand(1, 1000)),
            'synced_at' => now(),
            'meta' => [],
            'source_id' => Source::factory(),
        ];
    }
}
