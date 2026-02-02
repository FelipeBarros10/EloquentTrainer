<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actor_movie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->string('role_name')->nullable();
            $table->unsignedSmallInteger('billing_order')->nullable();
            $table->boolean('is_lead')->default(false);
            $table->timestamps();

            $table->unique(['actor_id', 'movie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actor_movie');
    }
};
