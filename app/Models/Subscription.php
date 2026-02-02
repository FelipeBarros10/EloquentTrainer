<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'price_cents',
        'currency',
        'status',
        'provider',
        'external_id',
        'auto_renew',
        'started_at',
        'renews_at',
        'ends_at',
        'last_billed_at',
    ];

    protected $casts = [
        'auto_renew' => 'boolean',
        'started_at' => 'datetime',
        'renews_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_billed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
