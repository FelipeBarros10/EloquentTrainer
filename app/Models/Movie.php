<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'director_id',
        'title',
        'slug',
        'synopsis',
        'release_year',
        'release_date',
        'runtime_minutes',
        'language',
        'country',
        'age_rating',
        'is_streaming',
        'streaming_start_date',
        'budget',
        'revenue',
        'imdb_id',
        'avg_rating',
        'ratings_count',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_streaming' => 'boolean',
        'streaming_start_date' => 'date',
        'avg_rating' => 'decimal:2',
    ];

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class)
            ->withPivot(['role_name', 'billing_order', 'is_lead'])
            ->withTimestamps();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
