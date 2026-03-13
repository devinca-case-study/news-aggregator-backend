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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('external_id');
            
            $table->foreignId('source_id')
                ->constrained('sources')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('url');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('synced_at')->useCurrent();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'external_id']);
            $table->index('source_id');
            $table->index('published_at');
            $table->index(['source_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
