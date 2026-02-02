<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('director_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis')->nullable();
            $table->unsignedSmallInteger('release_year')->index();
            $table->date('release_date')->nullable();
            $table->unsignedSmallInteger('runtime_minutes')->nullable();
            $table->string('language', 5)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('age_rating', 10)->nullable();
            $table->boolean('is_streaming')->default(false);
            $table->date('streaming_start_date')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->unsignedBigInteger('revenue')->nullable();
            $table->string('imdb_id')->nullable()->unique();
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
