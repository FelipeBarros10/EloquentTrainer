<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Actor extends Model
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

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class)
            ->withPivot(['role_name', 'billing_order', 'is_lead'])
            ->withTimestamps();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
