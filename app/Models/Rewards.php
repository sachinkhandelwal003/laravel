<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Rewards extends Model
{
    use HasFactory, SoftDeletes;

  
    protected $fillable = [
        'user_id',
        'reward_type',
        'amount',
        'code',
        'status',
        'valid_at'
    ];

    // Automatically cast valid_at as a Carbon date instance
    protected $casts = [
        'valid_at' => 'date',
    ];

    // Appending custom attribute is_expired to the model's array form
    protected $appends = ['is_expired'];

    /**
     * Check if the reward has expired
     *
     * @return bool
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_at ? $this->valid_at->isPast() : false;
    }


    /**
     * Scope for active (non-expired) rewards
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereDate('valid_at', '>=', now());
    }

    /**
     * Scope for expired rewards
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('valid_at', '<', now());
    }
}
