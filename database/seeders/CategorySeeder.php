<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Business', 'Entertainment', 'General', 'Health', 'Science', 'Sports', 'Technology', 'Politics', 'Lifestyle'];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate([
                'code' => Str::slug($category)
            ], [
                'name' => $category
            ]);
        }
    }
}
