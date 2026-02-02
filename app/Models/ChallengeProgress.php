<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeProgress extends Model
{
    protected $table = 'challenge_progress';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'completed',
        'points_awarded',
        'attempts',
        'last_code',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

