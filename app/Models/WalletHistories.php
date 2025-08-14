<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletHistories extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'user_id',
        'type',
        'points',
        'points_type',
        'deposit',
        'withdrawal',
        'referral_bonus'
  
    ];
    
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }

}
