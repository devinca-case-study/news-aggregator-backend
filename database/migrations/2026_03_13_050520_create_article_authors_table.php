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
        Schema::create('article_authors', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->constrained('articles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('author_id')
                ->constrained('authors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->primary(['article_id', 'author_id']);
            $table->index(['author_id', 'article_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_authors');
    }
};
