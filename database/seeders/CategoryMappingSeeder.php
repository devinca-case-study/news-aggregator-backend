<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryMapping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            // NewsAPI
            'business' => 'business',
            'entertainment' => 'entertainment',
            'general' => 'general',
            'health' => 'health',
            'science' => 'science',
            'sports' => 'sports',
            'technology' => 'technology',

            // NYTimes
            'Arts' => 'entertainment',
            'Movies' => 'entertainment',
            'Theater' => 'entertainment',
            'Fashion' => 'entertainment',
            'Food' => 'entertainment',
            'Travel' => 'entertainment',

            'Business' => 'business',
            'Real Estate' => 'business',
            'Automobiles' => 'business',

            'Science' => 'science',
            'Technology' => 'technology',
            'Sports' => 'sports',
            'Health' => 'health',

            'US' => 'general',
            'World' => 'general',
            'Politics' => 'general',
            'Opinion' => 'general',
            'Home' => 'general',
            'NYRegion' => 'general',
            'Magazine' => 'general',
            'Insider' => 'general',
            'Obituaries' => 'general',

            // The Guardian
            'world' => 'general',
            'politics' => 'general',
            'environment' => 'science',
            'education' => 'general',
            'society' => 'general',
            'media' => 'general',
            'lifeandstyle' => 'general',

            'sport' => 'sports',

            'film' => 'entertainment',
            'music' => 'entertainment',
            'games' => 'entertainment',
            'books' => 'entertainment',
            'culture' => 'entertainment',
            'fashion' => 'entertainment',
            'travel' => 'entertainment',
            'food' => 'entertainment',
        ];

        foreach ($mappings as $raw => $code) {
            $category = Category::query()
                ->where('code', $code)
                ->first();

            if (!$category) {
                continue;
            }

            CategoryMapping::query()->updateOrCreate(
                ['raw_name' => Str::slug($raw)],
                ['category_id' => $category->id]
            );
        }
    }
}
