<?php

namespace App\Models;


use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CleanerReferral extends Model
{
      use HasFactory, SoftDeletes, CustomScopes;
      
      
    protected $fillable = [
        'referral_code', 'cleaner_id', 'reward','generated_by'
    ];
    
    public function cleaner()
{
    return $this->belongsTo(Cleaner::class);
}

}
