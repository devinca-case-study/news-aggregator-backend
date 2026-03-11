<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_unmapped_categories', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->constrained('articles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('unmapped_category_id')
                ->constrained('unmapped_categories')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->primary(['article_id', 'unmapped_category_id']);

            $table->timestamps();
            
            $table->index('unmapped_category_id');
            $table->index(['unmapped_category_id', 'article_id'], 'article_unmapped_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_unmapped_categories');
    }
};
