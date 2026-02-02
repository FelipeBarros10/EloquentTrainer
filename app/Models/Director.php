<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Director extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'birthdate',
        'country',
        'imdb_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
