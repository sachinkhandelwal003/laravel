<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'description',
        'is_read'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'submission_date' => 'datetime'
    ];

    /**
     * The default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_read' => false,
    ];

    /**
     * Scope a query to only include read inquiries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unread inquiries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to search inquiries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%')
                     ->orWhere('email', 'like', '%'.$search.'%')
                     ->orWhere('description', 'like', '%'.$search.'%');
    }

    /**
     * Mark the inquiry as read.
     *
     * @return bool
     */
    public function markAsRead()
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark the inquiry as unread.
     *
     * @return bool
     */
    public function markAsUnread()
    {
        return $this->update(['is_read' => false]);
    }
}