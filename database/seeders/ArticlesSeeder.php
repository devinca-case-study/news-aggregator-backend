<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = Source::all();
        $authors = Author::all();
        $categories = Category::all();

        for ($i = 1; $i <= 30; $i++) {

            $source = $sources->random();

            $article = Article::query()->create([
                'provider' => 'seeder',
                'external_id' => Str::uuid(),
                'source_id' => $source->id,
                'url' => "https://example.com/articles/{$i}",
                'title' => "Sample Article Title {$i}",
                'content' => "This is sample content for article {$i}. It is used for testing filtering and ranking.",
                'published_at' => now()->subDays(rand(0, 10)),
                'meta' => json_encode([
                    'seeded' => true
                ])
            ]);

            $article->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );

            $article->authors()->attach(
                $authors->random(rand(1, 2))->pluck('id')->toArray()
            );
        }
    }
}
