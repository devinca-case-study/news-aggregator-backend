<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryMapping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryMappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category mappings are based on provider documentation:
        // - NewsAPI categories: https://newsapi.org/docs/endpoints/sources
        // - NYTimes sections: https://developer.nytimes.com/docs/articlesearch-product/1/overview
        // - TheGuardian sections: https://content.guardianapis.com/sections
        
        // Mapping to internal categories is defined manually.

        $mappings = [
            'newsapi' => [
                'business' => 'business',
                'entertainment' => 'entertainment',
                'general' => 'general',
                'health' => 'health',
                'science' => 'science',
                'sports' => 'sports',
                'technology' => 'technology'
            ],
            'nytimes' => [
                'Arts' => 'entertainment',
                'Books' => 'entertainment',
                'Briefing' => 'general',
                'Climate' => 'science',
                'Corrections' => 'general',
                'En español' => 'general',
                'Fashion' => 'lifestyle',
                'Food' => 'lifestyle',
                'Gameplay' => 'entertainment',
                'Guide' => 'technology',
                'Headway' => 'general',
                'Health' => 'health',
                'Home Page' => 'general',
                'Job Market' => 'business',
                'Lens' => 'general',
                'Magazine' => 'entertainment',
                'Movies' => 'entertainment',
                'Multimedia/Photos' => 'general',
                'New York' => 'general',
                'Obituaries' => 'general',
                'Opinion' => 'politics',
                'Parenting' => 'lifestyle',
                'Podcasts' => 'entertainment',
                'Reader Center' => 'general',
                'Real Estate' => 'business',
                'Science' => 'science',
                'Smarter Living' => 'lifestyle',
                'Sports' => 'sports',
                'Style' => 'lifestyle',
                'Sunday Review' => 'entertainment',
                'T Brand' => 'general',
                'T Magazine' => 'entertainment',
                'The Learning Network' => 'general',
                'The New York Times Presents' => 'entertainment',
                'The Upshot' => 'business',
                'The Weekly' => 'entertainment',
                'Theater' => 'entertainment',
                'Times Insider' => 'general',
                "Today's Paper" => 'general',
                'Travel' => 'lifestyle',
                'U.S.' => 'politics',
                'Universal' => 'general',
                'Well' => 'lifestyle',
                'World' => 'politics',
                'Your Money' => 'business'
            ],
            'guardian' => [
                'better-business' => 'business',
                'business' => 'business',
                'jobsadvice' => 'business',
                'money' => 'business',

                'artanddesign' => 'entertainment',
                'books' => 'entertainment',
                'culture' => 'entertainment',
                'film' => 'entertainment',
                'games' => 'entertainment',
                'music' => 'entertainment',
                'stage' => 'entertainment',
                'tv-and-radio' => 'entertainment',

                'wellness' => 'health',

                'fashion' => 'lifestyle',
                'food' => 'lifestyle',
                'lifeandstyle' => 'lifestyle',
                'travel' => 'lifestyle',

                'commentisfree' => 'politics',
                'politics' => 'politics',

                'environment' => 'science',
                'science' => 'science',

                'football' => 'sports',
                'sport' => 'sports',

                'technology' => 'technology',

                'australia-news' => 'general',
                'education' => 'general',
                'global-development' => 'general',
                'media' => 'general',
                'news' => 'general',
                'society' => 'general',
                'uk-news' => 'general',
                'us-news' => 'general',
                'weather' => 'general',
                'world' => 'general'
            ],
        ];

        foreach ($mappings as $provider => $providerMappings) {
            foreach ($providerMappings as $raw => $code) {
                $category = Category::query()
                    ->where('code', $code)
                    ->first();

                if (!$category) {
                    continue;
                }

                CategoryMapping::query()->updateOrCreate([
                    'provider' => $provider,
                    'raw_name' => Str::slug($raw),
                ], [
                    'category_id' => $category->id
                ]);
            }
        }
    }
}
