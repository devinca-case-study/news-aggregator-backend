<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'business',
                'name' => 'Business',
            ],
            [
                'code' => 'entertainment',
                'name' => 'Entertainment',
            ],
            [
                'code' => 'general',
                'name' => 'General',
            ],
            [
                'code' => 'health',
                'name' => 'Health',
            ],
            [
                'code' => 'science',
                'name' => 'Science',
            ],
            [
                'code' => 'sports',
                'name' => 'Sports',
            ],
            [
                'code' => 'technology',
                'name' => 'Technology',
            ],
            [
                'code' => 'politics',
                'name' => 'Politics',
            ],
            [
                'code' => 'lifestyle',
                'name' => 'Lifestyle',
            ]
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate([
                'code' => $category['code']
            ], $category);
        }
    }
}
