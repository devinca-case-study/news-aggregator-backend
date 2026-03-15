<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_filter_articles_by_search_keyword(): void
    {
        $article1 = Article::factory()->create([
            'title' => 'Bitcoin hits new all-time high',
            'content' => 'Crypto market rallies strongly today.',
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Football match tonight',
            'content' => 'Sports fans are excited for the final.',
        ]);

        $response = $this->getJson('/api/articles?search=bitcoin');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $titles = collect($response->json('data'))->pluck('title');

        $this->assertTrue($titles->contains($article1->title));
        $this->assertFalse($titles->contains($article2->title));
    }

    public function test_it_can_filter_articles_by_category(): void
    {
        $technology = Category::factory()->create([
            'name' => 'Technology',
        ]);

        $sports = Category::factory()->create([
            'name' => 'Sports',
        ]);

        $techArticle = Article::factory()->create([
            'title' => 'New AI breakthrough announced',
        ]);
        $techArticle->categories()->attach($technology->id);

        $sportsArticle = Article::factory()->create([
            'title' => 'Champions league final result',
        ]);
        $sportsArticle->categories()->attach($sports->id);

        $response = $this->getJson('/api/articles?category_ids[]=' . $technology->id);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $titles = collect($response->json('data'))->pluck('title');

        $this->assertTrue($titles->contains($techArticle->title));
        $this->assertFalse($titles->contains($sportsArticle->title));
    }

    public function test_it_can_filter_articles_by_source(): void
    {
        $cnn = Source::factory()->create([
            'name' => 'CNN',
        ]);

        $guardian = Source::factory()->create([
            'name' => 'The Guardian',
        ]);

        $cnnArticle = Article::factory()->create([
            'title' => 'US economy update',
            'source_id' => $cnn->id,
        ]);

        $guardianArticle = Article::factory()->create([
            'title' => 'UK politics today',
            'source_id' => $guardian->id,
        ]);

        $response = $this->getJson('/api/articles?source_ids[]=' . $cnn->id);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $titles = collect($response->json('data'))->pluck('title');

        $this->assertTrue($titles->contains($cnnArticle->title));
        $this->assertFalse($titles->contains($guardianArticle->title));
    }

    public function test_it_returns_paginated_article_response(): void
    {
        Article::factory()->count(15)->create();

        $response = $this->getJson('/api/articles?per_page=10');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
                'meta',
            ]);

        $this->assertCount(10, $response->json('data'));
    }
}