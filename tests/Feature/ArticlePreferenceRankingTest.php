<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticlePreferenceRankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_preferred_category_articles_appear_first_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $technology = Category::factory()->create([
            'name' => 'Technology',
        ]);

        $sports = Category::factory()->create([
            'name' => 'Sports',
        ]);

        $preferredArticle = Article::factory()->create([
            'title' => 'AI is transforming productivity',
            'published_at' => now()->subHour(),
        ]);
        $preferredArticle->categories()->attach($technology->id);

        $nonPreferredArticle = Article::factory()->create([
            'title' => 'Football league standings updated',
            'published_at' => now(),
        ]);
        $nonPreferredArticle->categories()->attach($sports->id);

        $user->preferredCategories()->attach($technology->id);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/articles');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $data = $response->json('data');

        $this->assertNotEmpty($data);
        $this->assertEquals($preferredArticle->id, $data[0]['id']);
    }

    public function test_guest_user_gets_normal_order_without_preference_ranking(): void
    {
        $technology = Category::factory()->create([
            'name' => 'Technology',
        ]);

        $sports = Category::factory()->create([
            'name' => 'Sports',
        ]);

        $olderTechArticle = Article::factory()->create([
            'title' => 'Older technology news',
            'published_at' => now()->subDays(2),
        ]);
        $olderTechArticle->categories()->attach($technology->id);

        $newerSportsArticle = Article::factory()->create([
            'title' => 'Latest sports update',
            'published_at' => now(),
        ]);
        $newerSportsArticle->categories()->attach($sports->id);

        $response = $this->getJson('/api/articles');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $data = $response->json('data');

        $this->assertNotEmpty($data);
        $this->assertEquals($newerSportsArticle->id, $data[0]['id']);
    }

    public function test_preference_ranking_still_respects_active_filters(): void
    {
        $user = User::factory()->create();

        $technology = Category::factory()->create([
            'name' => 'Technology',
        ]);

        $sports = Category::factory()->create([
            'name' => 'Sports',
        ]);

        $techArticle = Article::factory()->create([
            'title' => 'Technology headline',
        ]);
        $techArticle->categories()->attach($technology->id);

        $sportsArticle = Article::factory()->create([
            'title' => 'Sports headline',
        ]);
        $sportsArticle->categories()->attach($sports->id);

        $user->preferredCategories()->attach($technology->id);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/articles?category_ids[]=' . $sports->id);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $titles = collect($response->json('data'))->pluck('title');

        $this->assertTrue($titles->contains($sportsArticle->title));
        $this->assertFalse($titles->contains($techArticle->title));
    }
}
